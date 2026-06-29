<?php
// ==================================================================
// Script de Diagnóstico de Ambiente para Laravel
// ==================================================================

ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h1>Diagnóstico do Ambiente do Servidor</h1>";
$basePath = __DIR__;
echo "<p>Raiz do projeto: <strong>" . $basePath . "</strong></p><hr>";

function check($description, $condition) {
    if ($condition) {
        echo "<p style='color:green; margin: 5px 0;'>✅ SUCESSO: $description</p>";
    } else {
        echo "<p style='color:red; margin: 5px 0;'>❌ FALHA: $description</p>";
    }
}

// 1. Verificação da Versão do PHP
echo "<h2>1. PHP</h2>";
$php_version = phpversion();
check("Versão do PHP é $php_version.", true);
check("Versão do PHP é 8.2 ou superior (requerido pelo Laravel 12)", version_compare($php_version, '8.2.0', '>='));
echo "<br>";

// 2. Verificação de Arquivos Essenciais
echo "<h2>2. Estrutura de Arquivos</h2>";
check("Arquivo .env existe", file_exists($basePath . '/.env'));
check("Pasta vendor existe", file_exists($basePath . '/vendor'));
check("Arquivo de autoload do Composer existe", file_exists($basePath . '/vendor/autoload.php'));
echo "<br>";

// 3. Verificação de Permissões de Escrita
echo "<h2>3. Permissões de Pasta</h2>";
$storage_path = $basePath . '/storage';
$bootstrap_cache_path = $basePath . '/bootstrap/cache';
check("Pasta 'storage' ('$storage_path') tem permissão de escrita", is_writable($storage_path));
check("Pasta 'bootstrap/cache' ('$bootstrap_cache_path') tem permissão de escrita", is_writable($bootstrap_cache_path));
echo "<br>";

echo "<hr><h2>Conclusão</h2>";
echo "<p>Revise os itens marcados com ❌. Eles são as causas mais prováveis do erro 500.</p>";
echo "<p style='color:blue; font-weight:bold;'>Lembre-se de deletar este arquivo após o uso.</p>";
