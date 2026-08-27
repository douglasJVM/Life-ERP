<?php
namespace App\Config;

class Auth {
    public static function validateToken(): int {
        // Mock de autenticação para desenvolvimento local (retorna o ID do usuário padrão)
        // Futuramente pode ser substituído por verificação de JWT ou Session
        return 1;
    }

    public static function getSecretKey(): string {
        return "seu_segredo_super_seguro_jwt_aqui";
    }
}