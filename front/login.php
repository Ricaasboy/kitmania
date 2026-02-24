<?php
// Início do processamento PHP
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

ob_start();
include("../back/conn.php");

// Verificar Symfony Mailer
$autoloadPath = __DIR__ . '/../vendor/autoload.php';
if (!file_exists($autoloadPath)) {
    ob_end_clean();
    die("❌ Symfony Mailer não instalado. Rode: composer require symfony/mailer");
}
require_once $autoloadPath;

use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Address;

// Se já estiver logado, redireciona
if (isset($_SESSION['user_id'])) {
    header("Location: home.php");
    exit;
}

// Processamento POST para todas as ações
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // LOGIN TRADICIONAL
    if (isset($_POST['email_login']) && isset($_POST['password_login'])) {
        $email = trim($_POST['email_login']);
        $senha = $_POST['password_login'];

        $stmt = $conn->prepare("SELECT id, full_name, email, password_hash, is_admin FROM users WHERE email = ? AND is_verified = 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            if (password_verify($senha, $row['password_hash'])) {
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['user_name'] = $row['full_name'];
                $_SESSION['user_email'] = $row['email'];
                $_SESSION['admin']   = $row['is_admin'] == 1;

                header("Location: home.php");
                exit;
            }
        }

        // Se chegou aqui, login falhou
        $erro_login = true;
    }

    // Processamento AJAX
    if (isset($_POST['acao'])) {
        ob_end_clean();
        header('Content-Type: application/json');
        error_reporting(0);

        // REGISTRAR
        if ($_POST['acao'] === 'registrar') {
            $nome = trim($_POST['name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $senha = $_POST['password'] ?? '';

            $resposta = ['success' => false, 'mensagem' => ''];

            try {
                if (empty($nome) || empty($email) || empty($senha)) {
                    throw new Exception('Todos os campos são obrigatórios.');
                }

                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    throw new Exception('Formato de email inválido.');
                }

                if (strlen($senha) < 8) {
                    throw new Exception('A palavra-passe deve ter pelo menos 8 caracteres.');
                }

                $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? UNION SELECT id FROM pending_registrations WHERE email = ?");
                $stmt->bind_param("ss", $email, $email);
                $stmt->execute();

                if ($stmt->get_result()->num_rows > 0) {
                    throw new Exception('Este email já está registado ou aguarda verificação.');
                }

                $codigo = sprintf("%06d", rand(0, 999999));
                $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
                $expiracao = date('Y-m-d H:i:s', strtotime('+15 minutes'));

                $stmt = $conn->prepare("
                    INSERT INTO pending_registrations 
                    (full_name, email, password_hash, verification_code, code_expires_at, created_at)
                    VALUES (?, ?, ?, ?, ?, NOW())
                ");
                $stmt->bind_param("sssss", $nome, $email, $senha_hash, $codigo, $expiracao);

                if ($stmt->execute()) {
                    try {
                        $dsn = 'smtp://sistema.kitmania@gmail.com:' . urlencode('yektlvbfyvaphxtf') . '@smtp.gmail.com:587?encryption=tls';
                        $transport = Transport::fromDsn($dsn);
                        $mailer = new Mailer($transport);

                        $html = '
                        <h2>Código de Ativação KitMania</h2>
                        <p>Olá <strong>' . htmlspecialchars($nome) . '</strong>,</p>
                        <p>O teu código de 6 dígitos é:</p>
                        <h1 style="font-size:48px;letter-spacing:10px;background:#33d286;color:white;padding:20px;text-align:center;border-radius:12px;">
                            ' . $codigo . '
                        </h1>
                        <p>Válido por 15 minutos. Insere no site para ativar a tua conta.</p>';

                        $emailMsg = (new Email())
                            ->from(new Address('sistema.kitmania@gmail.com', 'KitMania'))
                            ->to(new Address($email, $nome))
                            ->subject('Código KitMania - ' . $codigo)
                            ->html($html)
                            ->text("Código: $codigo\nVálido 15 min.");

                        $mailer->send($emailMsg);

                        $resposta = [
                            'success' => true,
                            'mensagem' => 'Código enviado para ' . $email . '. Verifica a tua caixa de entrada (ou spam).',
                            'email' => $email,
                            'nome' => $nome,
                            'expiracao' => strtotime('+15 minutes') * 1000
                        ];
                    } catch (Exception $e) {
                        $del = $conn->prepare("DELETE FROM pending_registrations WHERE email = ?");
                        $del->bind_param("s", $email);
                        $del->execute();

                        throw new Exception('Erro ao enviar o email: ' . $e->getMessage());
                    }
                } else {
                    throw new Exception('Erro ao guardar dados do registo: ' . $conn->error);
                }
            } catch (Exception $e) {
                $resposta['mensagem'] = $e->getMessage();
            }

            echo json_encode($resposta);
            exit;
        }

        // VERIFICAR CÓDIGO
        if ($_POST['acao'] === 'verificar') {
            $email = $_POST['email'] ?? '';
            $codigo = trim($_POST['codigo'] ?? '');

            $resposta = ['success' => false, 'mensagem' => ''];

            try {
                if (strlen($codigo) !== 6 || !ctype_digit($codigo)) {
                    throw new Exception('Código inválido (deve ter exatamente 6 dígitos numéricos).');
                }

                $stmt = $conn->prepare("
                    SELECT full_name, password_hash 
                    FROM pending_registrations 
                    WHERE email = ? AND verification_code = ? AND code_expires_at > NOW()
                ");
                $stmt->bind_param("ss", $email, $codigo);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($row = $result->fetch_assoc()) {
                    $ins = $conn->prepare("
                        INSERT INTO users (full_name, email, password_hash, is_verified, created_at)
                        VALUES (?, ?, ?, 1, NOW())
                    ");
                    $ins->bind_param("sss", $row['full_name'], $email, $row['password_hash']);

                    if ($ins->execute()) {
                        $user_id = $conn->insert_id;

                        $_SESSION['user_id'] = $user_id;
                        $_SESSION['user_name'] = $row['full_name'];
                        $_SESSION['user_email'] = $email;

                        $del = $conn->prepare("DELETE FROM pending_registrations WHERE email = ?");
                        $del->bind_param("s", $email);
                        $del->execute();

                        $resposta = [
                            'success' => true,
                            'mensagem' => 'Conta ativada! A redirecionar...',
                            'redirect' => 'home.php'
                        ];
                    } else {
                        throw new Exception('Erro ao criar a conta. Tente novamente.');
                    }
                } else {
                    throw new Exception('Código incorreto ou expirado.');
                }
            } catch (Exception $e) {
                $resposta['mensagem'] = $e->getMessage();
            }

            echo json_encode($resposta);
            exit;
        }

        // REENVIAR CÓDIGO
        if ($_POST['acao'] === 'reenviar') {
            $email = $_POST['email'] ?? '';

            $resposta = ['success' => false, 'mensagem' => ''];

            try {
                $stmt = $conn->prepare("SELECT id, full_name FROM pending_registrations WHERE email = ?");
                $stmt->bind_param("s", $email);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($row = $result->fetch_assoc()) {
                    $novoCodigo = sprintf("%06d", rand(0, 999999));
                    $expiracao = date('Y-m-d H:i:s', strtotime('+15 minutes'));

                    $update = $conn->prepare("UPDATE pending_registrations SET verification_code = ?, code_expires_at = ? WHERE email = ?");
                    $update->bind_param("sss", $novoCodigo, $expiracao, $email);

                    if ($update->execute()) {
                        try {
                            $dsn = 'smtp://sistema.kitmania@gmail.com:' . urlencode('yektlvbfyvaphxtf') . '@smtp.gmail.com:587?encryption=tls';
                            $transport = Transport::fromDsn($dsn);
                            $mailer = new Mailer($transport);

                            $html = '
                            <h2>Código de Ativação KitMania (Reenvio)</h2>
                            <p>Olá <strong>' . htmlspecialchars($row['full_name']) . '</strong>,</p>
                            <p>O teu novo código de 6 dígitos é:</p>
                            <h1 style="font-size:48px;letter-spacing:10px;background:#33d286;color:white;padding:20px;text-align:center;border-radius:12px;">
                                ' . $novoCodigo . '
                            </h1>
                            <p>Válido por 15 minutos. Insere no site para ativar a tua conta.</p>';

                            $emailMsg = (new Email())
                                ->from(new Address('sistema.kitmania@gmail.com', 'KitMania'))
                                ->to(new Address($email, $row['full_name']))
                                ->subject('Código KitMania - ' . $novoCodigo)
                                ->html($html)
                                ->text("Código: $novoCodigo\nVálido 15 min.");

                            $mailer->send($emailMsg);

                            $resposta = [
                                'success' => true,
                                'mensagem' => 'Novo código enviado para ' . $email,
                                'expiracao' => strtotime('+15 minutes') * 1000
                            ];
                        } catch (Exception $e) {
                            throw new Exception('Erro ao enviar o email: ' . $e->getMessage());
                        }
                    } else {
                        throw new Exception('Erro ao atualizar o código.');
                    }
                } else {
                    throw new Exception('Não foi encontrado nenhum registo pendente para este email.');
                }
            } catch (Exception $e) {
                $resposta['mensagem'] = $e->getMessage();
            }

            echo json_encode($resposta);
            exit;
        }

        echo json_encode(['success' => false, 'mensagem' => 'Ação desconhecida.']);
        exit;
    }
}

ob_end_flush();
?>

<!DOCTYPE HTML>
<html lang="en-US">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - KitMania</title>
    <link href="https://fonts.googleapis.com/css?family=Lato:300,400,500,600,700,800" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        #preloader {
            position: fixed;
            inset: 0;
            background: rgba(255, 255, 255, 0.85);
            z-index: 9999;
            display: none;
            justify-content: center;
            align-items: center;
        }

        #preloader .spinner {
            border: 8px solid #f3f3f3;
            border-top: 8px solid #33d286;
            border-radius: 50%;
            width: 60px;
            height: 60px;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        .form-transition {
            transition: opacity 0.6s ease, transform 0.6s ease;
        }

        .form-hidden {
            opacity: 0;
            transform: translateY(20px);
            pointer-events: none;
            position: absolute;
            width: 100%;
            top: 0;
            left: 0;
        }

        .form-visible {
            opacity: 1;
            transform: translateY(0);
            position: relative;
        }

        #feedback-msg {
            max-width: 600px;
            margin: 20px auto;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
            display: none;
        }

        .success-msg {
            background: #e6f9f0;
            color: #1e7d55;
            border: 1px solid #1e7d55;
        }

        .error-msg {
            background: #ffebee;
            color: #c62828;
            border: 1px solid #c62828;
        }

        .verification-code-container {
            text-align: center;
            margin: 40px 0;
        }

        .verification-title {
            font-size: 28px;
            color: #333;
            margin-bottom: 15px;
            font-weight: 600;
        }

        .verification-subtitle {
            color: #666;
            margin-bottom: 25px;
            font-size: 16px;
        }

        .email-highlight {
            font-weight: bold;
            color: #33d286;
            background: #f0fff9;
            padding: 5px 10px;
            border-radius: 6px;
            display: inline-block;
            margin: 5px 0;
        }

        .code-inputs {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin: 30px auto;
            max-width: 400px;
        }

        .code-digit {
            width: 60px;
            height: 70px;
            font-size: 32px;
            text-align: center;
            border: 2px solid #ddd;
            border-radius: 10px;
            background: #f9f9f9;
            transition: all 0.3s;
            outline: none;
            font-weight: bold;
        }

        .code-digit:focus {
            border-color: #33d286;
            background: white;
            box-shadow: 0 0 0 3px rgba(51, 210, 134, 0.2);
            transform: scale(1.05);
        }

        .code-digit.filled {
            border-color: #33d286;
            background: #f0fff9;
        }

        #timer-container {
            text-align: center;
            margin: 20px 0;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 10px;
            max-width: 300px;
            margin: 20px auto;
        }

        #timer {
            font-size: 18px;
            color: #333;
            font-weight: 600;
        }

        .timer-expired {
            color: #dc3545 !important;
            animation: pulse 1.5s infinite;
        }

        @keyframes pulse {
            0% {
                opacity: 1;
            }

            50% {
                opacity: 0.6;
            }

            100% {
                opacity: 1;
            }
        }

        .verification-buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-top: 30px;
        }

        .btn-verificar {
            background: #33d286;
            color: white;
            border: none;
            padding: 15px 30px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .btn-verificar:hover {
            background: #2bb775;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(51, 210, 134, 0.3);
        }

        .btn-verificar:disabled {
            background: #ccc;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        .btn-reenviar {
            background: #6c757d;
            color: white;
            border: none;
            padding: 15px 25px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .btn-reenviar:hover {
            background: #5a6268;
            transform: translateY(-2px);
        }

        .check-spam {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
            color: #666;
        }

        .check-spam i {
            color: #33d286;
            margin-right: 5px;
        }

        .password-checklist {
            list-style: none;
            padding: 10px 0 0 20px;
            margin: 5px 0;
        }

        .password-checklist li {
            margin: 5px 0;
            font-size: 13px;
            position: relative;
        }

        .password-checklist li:before {
            content: "✗";
            color: #dc3545;
            position: absolute;
            left: -20px;
        }

        .password-checklist li.valid:before {
            content: "✓";
            color: #33d286;
        }

        .login-error-msg {
            background: #ffebee;
            color: #c62828;
            border: 1px solid #c62828;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
            text-align: center;
            font-size: 14px;
            display: <?php echo isset($erro_login) ? 'block' : 'none'; ?>;
        }

        #btn-login {
            cursor: pointer;
            transition: all 0.3s;
        }

        #btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(51, 210, 134, 0.3);
        }
    </style>
</head>

<body>

    <?php @include("header.php"); ?>

    <!-- Mensagem de feedback -->
    <div id="feedback-msg"></div>

    <!-- Login Page Area -->
    <div class="login_page_area">
        <div class="container">
            <div class="row">
                <!-- Coluna Esquerda - Registo -->
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <div class="create_account_area caa_pdngbtm" style="position:relative; min-height:600px;">

                        <h2 class="caa_heading">
                            <?php echo $lang === 'en' ? 'Create an Account' : 'Criar uma Conta'; ?>
                        </h2>

                        <!-- Formulário de Registo -->
                        <div id="form-registo" class="caa_form_area form-transition form-visible">
                            <form id="registo-form">
                                <div class="caa_form_group">
                                    <div class="caf_form">
                                        <!-- Nome -->
                                        <div class="form-group">
                                            <label for="register_name">
                                                <?php echo $lang === 'en' ? 'Full Name' : 'Nome Completo'; ?>
                                            </label>
                                            <div class="input-area">
                                                <input type="text" id="register_name" name="name"
                                                    placeholder="<?php echo $lang === 'en' ? 'Your full name' : 'O teu nome completo'; ?>"
                                                    required />
                                            </div>
                                        </div>

                                        <!-- Email -->
                                        <div class="form-group">
                                            <label for="register_email">
                                                <?php echo $lang === 'en' ? 'Email' : 'Email'; ?>
                                            </label>
                                            <div class="input-area">
                                                <input type="email" id="register_email" name="email"
                                                    placeholder="<?php echo $lang === 'en' ? 'example@example.com' : 'exemplo@exemplo.com'; ?>"
                                                    required />
                                            </div>
                                        </div>

                                        <!-- Palavra-passe -->
                                        <div class="form-group">
                                            <label for="register_password">
                                                <?php echo $lang === 'en' ? 'Password' : 'Palavra-passe'; ?>
                                            </label>
                                            <div class="input-area">
                                                <input type="password" id="register_password" name="password"
                                                    placeholder="••••••••" required minlength="8" />
                                            </div>

                                            <!-- Checklist de password -->
                                            <ul id="password-requirements" class="password-checklist">
                                                <li id="req-length" class="invalid">
                                                    <?php echo $lang === 'en' ? 'At least 8 characters' : 'Pelo menos 8 caracteres'; ?>
                                                </li>
                                                <li id="req-uppercase" class="invalid">
                                                    <?php echo $lang === 'en' ? 'At least one uppercase letter' : 'Pelo menos uma letra maiúscula'; ?>
                                                </li>
                                                <li id="req-number" class="invalid">
                                                    <?php echo $lang === 'en' ? 'At least one number' : 'Pelo menos um número'; ?>
                                                </li>
                                                <li id="req-special" class="invalid">
                                                    <?php echo $lang === 'en' ? 'At least one special character' : 'Pelo menos um caractere especial'; ?>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>

                                    <button type="submit" class="btn btn-default acc_btn" id="btn-registar" disabled>
                                        <span>
                                            <i class="fa fa-user btn_icon"></i>
                                            <?php echo $lang === 'en' ? ' Create an Account ' : ' Criar uma Conta'; ?>
                                        </span>
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Formulário de Verificação de Código -->
                        <div id="form-codigo" class="caa_form_area form-transition form-hidden">
                            <div class="verification-code-container">
                                <h3 class="verification-title">
                                    <i class="fas fa-mail-bulk" style="color:#33d286; margin-right:10px;"></i>
                                    <?php echo $lang === 'en' ? 'Verify Your Email' : 'Verifica o Teu Email'; ?>
                                </h3>

                                <p class="verification-subtitle">
                                    <?php echo $lang === 'en' ? 'Enter the 6-digit code sent to:' : 'Insira o código de 6 dígitos enviado para:'; ?>
                                </p>

                                <div class="email-highlight" id="email-destino"></div>

                                <!-- Inputs de código -->
                                <div class="code-inputs">
                                    <input type="text" class="code-digit" maxlength="1" data-index="1" inputmode="numeric">
                                    <input type="text" class="code-digit" maxlength="1" data-index="2" inputmode="numeric">
                                    <input type="text" class="code-digit" maxlength="1" data-index="3" inputmode="numeric">
                                    <input type="text" class="code-digit" maxlength="1" data-index="4" inputmode="numeric">
                                    <input type="text" class="code-digit" maxlength="1" data-index="5" inputmode="numeric">
                                    <input type="text" class="code-digit" maxlength="1" data-index="6" inputmode="numeric">
                                </div>

                                <input type="hidden" id="codigo-completo">

                                <!-- Timer -->
                                <div id="timer-container">
                                    <div id="timer">
                                        <i class="fas fa-clock"></i>
                                        <?php echo $lang === 'en' ? 'Code expires in: ' : 'Código expira em: '; ?>
                                        <span id="timer-minutes">15</span>:<span id="timer-seconds">00</span>
                                    </div>
                                </div>

                                <!-- Botões -->
                                <div class="verification-buttons">
                                    <button id="btn-verificar-codigo" class="btn-verificar" disabled>
                                        <i class="fas fa-check-circle"></i>
                                        <?php echo $lang === 'en' ? 'Verify Code' : 'Verificar Código'; ?>
                                    </button>
                                    <button id="btn-reenviar-codigo" class="btn-reenviar">
                                        <i class="fas fa-redo"></i>
                                        <?php echo $lang === 'en' ? 'Resend Code' : 'Reenviar Código'; ?>
                                    </button>
                                </div>

                                <!-- Voltar ao registo -->
                                <p style="text-align:center; margin-top:25px;">
                                    <a href="#" id="voltar-registo" style="color:#6c757d; text-decoration:none;">
                                        <i class="fas fa-arrow-left"></i>
                                        <?php echo $lang === 'en' ? 'Back to registration' : 'Voltar ao registo'; ?>
                                    </a>
                                </p>

                                <!-- Aviso spam -->
                                <div class="check-spam">
                                    <i class="fas fa-exclamation-circle"></i>
                                    <?php echo $lang === 'en' ? 'Not receiving the code? Check your spam folder.' : 'Não está a receber o código? Verifique a pasta de spam.'; ?>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- Coluna Direita - Login -->
                <div class="col-md-6 col-sm-6 col-xs-12">
                    <div class="create_account_area">
                        <h2 class="caa_heading">
                            <?php echo $lang === 'en' ? 'Already registered?' : 'Já está registado?'; ?>
                        </h2>

                        <!-- Mensagem de erro de login -->
                        <div class="login-error-msg">
                            <i class="fas fa-exclamation-triangle"></i>
                            <?php echo $lang === 'en' ? 'Email or password incorrect!' : 'Email ou palavra-passe incorretos!'; ?>
                        </div>

                        <div class="caa_form_area">
                            <form method="POST" action="">
                                <div class="caa_form_group">
                                    <div class="login_email">
                                        <label>
                                            <?php echo $lang === 'en' ? 'Email' : 'Email'; ?>
                                        </label>
                                        <div class="input-area">
                                            <input type="email" name="email_login"
                                                placeholder="<?php echo $lang === 'en' ? 'example@example.com' : 'exemplo@exemplo.com'; ?>"
                                                required />
                                        </div>
                                    </div>
                                    <div class="login_password">
                                        <label>
                                            <?php echo $lang === 'en' ? 'Password' : 'Palavra-passe'; ?>
                                        </label>
                                        <div class="input-area">
                                            <input type="password" name="password_login" placeholder="••••••••" required
                                                minlength="8" />
                                        </div>
                                    </div>
                                    <p class="forgot_password">
                                        <a href="recuperar_senha.php" title="Recover your forgotten password">
                                            <?php echo $lang === 'en' ? 'Forgot your password?' : 'Esqueceu a palavra-passe?'; ?>
                                        </a>
                                    </p>
                                    <button type="submit" name="login" class="btn btn-default acc_btn" id="btn-login">
                                        <span>
                                            <i class="fa fa-lock btn_icon"></i>
                                            <?php echo $lang === 'en' ? 'Sign in' : 'Entrar'; ?>
                                        </span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!--  Preloader  -->

    <div class="preloader">
        <div class="status-mes">
            <div class="bigSqr">
                <div class="square first"></div>
                <div class="square second"></div>
                <div class="square third"></div>
                <div class="square fourth"></div>
            </div>
            <div class="text_loading text-center">loading</div>
        </div>
    </div>

    <!-- Scripts originais -->
    <script src="../assets/js/vendor/jquery-1.12.4.min.js"></script>
    <script src="../assets/js/bootstrap.min.js"></script>
    <script src="../assets/js/jquery.meanmenu.min.js"></script>
    <script src="../assets/js/jquery.mixitup.js"></script>
    <script src="../assets/js/jquery.counterup.min.js"></script>
    <script src="../assets/js/waypoints.min.js"></script>
    <script src="../assets/js/wow.min.js"></script>
    <script src="../assets/js/venobox.min.js"></script>
    <script src="../assets/js/owl.carousel.min.js"></script>
    <script src="../assets/js/simplePlayer.js"></script>
    <script src="../assets/js/main.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Elementos principais
            const preloader = document.getElementById('preloader');
            const formRegisto = document.getElementById('form-registo');
            const formCodigo = document.getElementById('form-codigo');
            const feedback = document.getElementById('feedback-msg');
            const codigoDigits = document.querySelectorAll('.code-digit');
            const btnVerificar = document.getElementById('btn-verificar-codigo');
            const btnReenviar = document.getElementById('btn-reenviar-codigo');
            const voltarRegisto = document.getElementById('voltar-registo');
            const codigoCompleto = document.getElementById('codigo-completo');

            // Variáveis de estado
            let emailAtual = '';
            let timerInterval;
            let tempoExpiracao = 0;

            // Funções utilitárias
            function showLoading(show) {
                preloader.style.display = show ? 'flex' : 'none';
            }

            function mostrarFeedback(texto, tipo = 'success') {
                feedback.textContent = texto;
                feedback.className = tipo === 'success' ? 'success-msg' : 'error-msg';
                feedback.style.display = 'block';
                setTimeout(() => feedback.style.display = 'none', 5000);
            }

            function atualizarCodigoCompleto() {
                let codigo = '';
                codigoDigits.forEach(input => {
                    codigo += input.value;
                });
                codigoCompleto.value = codigo;

                // Ativar/desativar botão de verificar
                const todosPreenchidos = Array.from(codigoDigits).every(input => input.value !== '');
                btnVerificar.disabled = !todosPreenchidos;

                // Adicionar classe visual aos dígitos preenchidos
                codigoDigits.forEach(input => {
                    if (input.value) {
                        input.classList.add('filled');
                    } else {
                        input.classList.remove('filled');
                    }
                });
            }

            function iniciarTimer(timestampExpiracao) {
                if (timerInterval) clearInterval(timerInterval);

                tempoExpiracao = timestampExpiracao;
                const timerContainer = document.getElementById('timer-container');
                const timerText = document.getElementById('timer');

                timerInterval = setInterval(() => {
                    const agora = Date.now();
                    const restante = Math.max(0, tempoExpiracao - agora);

                    if (restante <= 0) {
                        clearInterval(timerInterval);
                        timerText.innerHTML = '<i class="fas fa-clock"></i> <span class="timer-expired">Código expirado</span>';
                        timerContainer.style.background = '#ffebee';
                        timerContainer.style.border = '1px solid #ffcdd2';
                        btnVerificar.disabled = true;
                        return;
                    }

                    const minutos = Math.floor(restante / 60000);
                    const segundos = Math.floor((restante % 60000) / 1000);

                    document.getElementById('timer-minutes').textContent = minutos.toString().padStart(2, '0');
                    document.getElementById('timer-seconds').textContent = segundos.toString().padStart(2, '0');
                }, 1000);
            }

            function mostrarFormCodigo(email, nome, expiracao) {
                emailAtual = email;
                document.getElementById('email-destino').textContent = email;

                // Transição suave entre formulários
                formRegisto.classList.remove('form-visible');
                formRegisto.classList.add('form-hidden');

                setTimeout(() => {
                    formCodigo.classList.remove('form-hidden');
                    formCodigo.classList.add('form-visible');

                    // Focar primeiro dígito
                    setTimeout(() => codigoDigits[0].focus(), 100);

                    // Iniciar timer
                    iniciarTimer(expiracao);
                }, 300);
            }

            function voltarParaRegisto() {
                formCodigo.classList.remove('form-visible');
                formCodigo.classList.add('form-hidden');

                setTimeout(() => {
                    formRegisto.classList.remove('form-hidden');
                    formRegisto.classList.add('form-visible');

                    // Limpar campos do código
                    codigoDigits.forEach(input => {
                        input.value = '';
                        input.classList.remove('filled');
                    });
                    atualizarCodigoCompleto();

                    // Limpar timer
                    if (timerInterval) clearInterval(timerInterval);
                }, 300);
            }

            // Eventos dos dígitos do código
            codigoDigits.forEach((input, index) => {
                // Focar próximo campo ao digitar
                input.addEventListener('input', (e) => {
                    if (e.target.value && index < codigoDigits.length - 1) {
                        codigoDigits[index + 1].focus();
                    }
                    atualizarCodigoCompleto();
                });

                // Permitir navegação com setas e backspace
                input.addEventListener('keydown', (e) => {
                    if (e.key === 'Backspace' && !e.target.value && index > 0) {
                        e.preventDefault();
                        codigoDigits[index - 1].focus();
                    } else if (e.key === 'ArrowLeft' && index > 0) {
                        e.preventDefault();
                        codigoDigits[index - 1].focus();
                    } else if (e.key === 'ArrowRight' && index < codigoDigits.length - 1) {
                        e.preventDefault();
                        codigoDigits[index + 1].focus();
                    } else if (e.key === 'Enter' && btnVerificar.disabled === false) {
                        e.preventDefault();
                        btnVerificar.click();
                    }
                });

                // Colar código completo
                input.addEventListener('paste', (e) => {
                    e.preventDefault();
                    const textoColado = e.clipboardData.getData('text').replace(/\D/g, '').substring(0, 6);

                    textoColado.split('').forEach((char, charIndex) => {
                        if (codigoDigits[charIndex]) {
                            codigoDigits[charIndex].value = char;
                            codigoDigits[charIndex].classList.add('filled');
                        }
                    });

                    atualizarCodigoCompleto();
                    if (textoColado.length === 6) {
                        btnVerificar.focus();
                    }
                });
            });

            // Voltar ao formulário de registo
            voltarRegisto.onclick = (e) => {
                e.preventDefault();
                voltarParaRegisto();
            };

            // Submeter registo via AJAX
            document.getElementById('registo-form').addEventListener('submit', async (e) => {
                e.preventDefault();
                showLoading(true);

                const formData = new FormData(e.target);
                formData.append('acao', 'registrar');

                try {
                    const resposta = await fetch('', {
                        method: 'POST',
                        body: formData
                    });

                    const texto = await resposta.text();
                    let dados;
                    try {
                        dados = JSON.parse(texto);
                    } catch (erroParse) {
                        const jsonMatch = texto.match(/\{.*\}/s);
                        dados = jsonMatch ? JSON.parse(jsonMatch[0]) : {
                            success: false,
                            mensagem: 'Resposta inválida'
                        };
                    }

                    if (dados.success) {
                        mostrarFeedback(dados.mensagem, 'success');
                        mostrarFormCodigo(dados.email, dados.nome, dados.expiracao);

                        // Resetar formulário de registo
                        e.target.reset();
                        document.getElementById('btn-registar').disabled = true;

                        // Resetar checklist de password
                        document.querySelectorAll('.password-checklist li').forEach(li => {
                            li.classList.remove('valid');
                            li.classList.add('invalid');
                        });

                    } else {
                        mostrarFeedback(dados.mensagem, 'error');
                    }
                } catch (erro) {
                    mostrarFeedback('Falha na comunicação com o servidor', 'error');
                } finally {
                    showLoading(false);
                }
            });

            // Verificar código via AJAX
            btnVerificar.onclick = async () => {
                const codigo = codigoCompleto.value;

                if (codigo.length !== 6 || !/^\d{6}$/.test(codigo)) {
                    mostrarFeedback('Código inválido (6 dígitos numéricos)', 'error');
                    return;
                }

                showLoading(true);

                const dadosVerificacao = new FormData();
                dadosVerificacao.append('acao', 'verificar');
                dadosVerificacao.append('email', emailAtual);
                dadosVerificacao.append('codigo', codigo);

                try {
                    const resposta = await fetch('', {
                        method: 'POST',
                        body: dadosVerificacao
                    });

                    const texto = await resposta.text();
                    let dados;
                    try {
                        dados = JSON.parse(texto);
                    } catch (erroParse) {
                        const jsonMatch = texto.match(/\{.*\}/s);
                        dados = jsonMatch ? JSON.parse(jsonMatch[0]) : {
                            success: false,
                            mensagem: 'Resposta inválida'
                        };
                    }

                    mostrarFeedback(dados.mensagem, dados.success ? 'success' : 'error');

                    if (dados.success) {
                        clearInterval(timerInterval);
                        setTimeout(() => {
                            window.location.href = dados.redirect || 'home.php';
                        }, 1500);
                    }
                } catch (erro) {
                    mostrarFeedback('Erro ao verificar o código', 'error');
                } finally {
                    showLoading(false);
                }
            };

            // Reenviar código
            btnReenviar.onclick = async () => {
                showLoading(true);

                const dadosReenvio = new FormData();
                dadosReenvio.append('acao', 'reenviar');
                dadosReenvio.append('email', emailAtual);

                try {
                    const resposta = await fetch('', {
                        method: 'POST',
                        body: dadosReenvio
                    });

                    const texto = await resposta.text();
                    let dados;
                    try {
                        dados = JSON.parse(texto);
                    } catch (erroParse) {
                        const jsonMatch = texto.match(/\{.*\}/s);
                        dados = jsonMatch ? JSON.parse(jsonMatch[0]) : {
                            success: false,
                            mensagem: 'Resposta inválida'
                        };
                    }

                    if (dados.success) {
                        mostrarFeedback('Novo código enviado com sucesso!', 'success');
                        iniciarTimer(dados.expiracao);

                        // Resetar campos do código
                        codigoDigits.forEach(input => {
                            input.value = '';
                            input.classList.remove('filled');
                        });
                        atualizarCodigoCompleto();
                        codigoDigits[0].focus();
                    } else {
                        mostrarFeedback(dados.mensagem, 'error');
                    }
                } catch (erro) {
                    mostrarFeedback('Erro ao reenviar código', 'error');
                } finally {
                    showLoading(false);
                }
            };
        });

        // Checklist de password
        document.addEventListener('DOMContentLoaded', function() {
            const passwordInput = document.getElementById('register_password');
            if (!passwordInput) return;

            const reqLength = document.getElementById('req-length');
            const reqUpper = document.getElementById('req-uppercase');
            const reqNumber = document.getElementById('req-number');
            const reqSpecial = document.getElementById('req-special');

            if (!reqLength || !reqUpper || !reqNumber || !reqSpecial) return;

            passwordInput.addEventListener('input', function(e) {
                const value = e.target.value;

                const hasLength = value.length >= 8;
                const hasUpper = /[A-Z]/.test(value);
                const hasNumber = /[0-9]/.test(value);
                const hasSpecial = /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?~`]/.test(value);
                const submitBtn = document.getElementById('btn-registar');
                const allValid = hasLength && hasUpper && hasNumber && hasSpecial;

                submitBtn.disabled = !allValid;

                reqLength.classList.toggle('valid', hasLength);
                reqLength.classList.toggle('invalid', !hasLength);

                reqUpper.classList.toggle('valid', hasUpper);
                reqUpper.classList.toggle('invalid', !hasUpper);

                reqNumber.classList.toggle('valid', hasNumber);
                reqNumber.classList.toggle('invalid', !hasNumber);

                reqSpecial.classList.toggle('valid', hasSpecial);
                reqSpecial.classList.toggle('invalid', !hasSpecial);
            });
        });
    </script>

</body>

</html>