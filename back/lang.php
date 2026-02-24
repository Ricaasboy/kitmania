<?php

// Idiomas suportados
$supported = ['pt', 'en'];

// Idioma PADRÃO / fallback → sempre inglês se nada for escolhido
$lang = 'en';

// 1. Prioridade máxima: utilizador escolheu AGORA via URL ?lang=...
if (isset($_GET['lang']) && in_array($_GET['lang'], $supported)) {
    $lang = $_GET['lang'];
    
    // Guardar na sessão para manter durante a navegação atual
    $_SESSION['lang'] = $lang;
}

// 2. Se não veio na URL → ver se já escolheu nesta sessão
elseif (isset($_SESSION['lang']) && in_array($_SESSION['lang'], $supported)) {
    $lang = $_SESSION['lang'];
}

// Função de tradução (igual)
function t($key, ...$args) {
    global $lang, $tr;
    $text = $tr[$lang][$key] ?? $key;  // devolve a chave se não encontrar tradução
    return $args ? vsprintf($text, $args) : $text;
}
?>