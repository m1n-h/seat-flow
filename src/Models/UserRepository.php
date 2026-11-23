<?php

declare(strict_types=1);

class UserRepository
{
  public function __construct(private PDO $pdo) {}

  public function findByEmail(string $email): ?array
  {
    $stmt = $this->pdo->prepare(
      "SELECT * FROM users WHERE email = :email LIMIT 1"
    );
    $stmt->execute(["email" => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    return $user !== false ? $user : null;
  }

  public function existsByEmail(string $email): bool
  {
    $stmt = $this->pdo->prepare("SELECT 1 FROM users WHERE email = :email LIMIT 1");
    $stmt->execute(['email' => $email]);

    return $stmt->fetchColumn() !== false;
  }

  public function create(string $name, string $email, string $password, string $role = 'user'): int
  {
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $this->pdo->prepare(
      "INSERT INTO users (name, email, password, role, created_at)
      VALUES (:name, :email, :password, :role, NOW())"
    );
    $stmt->execute([
      'name' => $name,
      'email' => $email,
      'password' => $hashedPassword,
      'role' => $role,
    ]);

    return (int)$this->pdo->lastInsertId();
  }
}
