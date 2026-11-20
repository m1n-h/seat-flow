<?php

declare(strict_types=1);

require_once __DIR__ . "/../Models/UserRepository.php";

class AuthController
{
  private UserRepository $userRepository;

  public function __construct(private PDO $pdo)
  {
    $this->userRepository = new UserRepository($pdo);
  }

  public function showLogin(): void
  {
    $errorMessage = $_SESSION['flash_error'] ?? null;
    $successMessage = $_SESSION['flash_success'] ?? null;
    $oldEmail = $_SESSION['old_email'] ?? '';

    unset($_SESSION['flash_error'], $_SESSION['flash_success'], $_SESSION['old_email']);

    require __DIR__ . '/../Views/auth/login.php';
  }

  public function showRegister(): void
  {
    $errorMessage = $_SESSION['flash_error'] ?? null;
    $successMessage = $_SESSION['flash_success'] ?? null;
    $oldEmail = $_SESSION['old_email'] ?? '';

    unset($_SESSION['flash_error'], $_SESSION['flash_success'], $_SESSION['old_email']);

    require __DIR__ . '/../Views/auth/register.php';
  }

  public function register(): void
  {
    $name = trim((string)($_POST['name'] ?? ''));
    $email = strtolower(trim((string)($_POST['email'] ?? '')));
    $password = (string)($_POST['password'] ?? '');
    $passwordConfirm = (string)($_POST['password_confirm'] ?? '');

    $_SESSION['old_name'] = $name;
    $_SESSION['dol_email'] = $email;

    if ($name === '' || mb_strlen($name) < 2) {
      $_SESSION['flash_error'] = "이름은 2자 이상 입력해 주세요.";
      header('Location: /?page=register');
      exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      $_SESSION['flash_error'] = "올바른 이메일 형식을 입력해 주세요.";
      header('Location: /?page=register');
      exit;
    }

    if (strlen($password) < 6) {
      $_SESSION['flash_error'] = "비밀번호는 6자 이상 입력해 주세요.";
      header('Location: /?page=register');
      exit;
    }

    if ($password !== $passwordConfirm) {
      $_SESSION['flash_error'] = "비밀번호가 일치하지 않습니다.";
      header('Location: /?page=register');
      exit;
    }

    try {
      if ($this->userRepository->existsByEmail($email)) {
        $_SESSION['flash_error'] = "이미 가입된 이메일 입니다.";
        header('Location: /?page=register');
        exit;

        $this->userRepository->create($name, $email, $password, 'user');
        unset($_SESSION['old_name'], $_SESSION['old_email']);
        $_SESSION['flash_success'] = "회원가입이 완료되었습니다. 로그인 해주세요.";
        header('Location: /?page=login');
        exit;
      }
    } catch (Throwable $e) {
      $_SESSION['flash_error'] = "회원가입 처리 중 오류가 발생했습니다.";
      header('Location: /?page=register');
      exit;
    }
  }

  public function login(): void
  {
    $email = strtolower(trim((string)($_POST['email'] ?? '')));
    $password = (string)($_POST['password'] ?? '');

    $_SESSION['old_email'] = $email;

    if ($email === '' || $password === '') {
      $_SESSION['flash_error'] = "이메일과 비밀번호 모두 입력 해주세요.";
      header('Location: /?page=login');
      exit;
    }

    try {
      $user = $this->userRepository->findByEmail($email);

      if ($user === null || !password_verify($password, (string)$user['password'])) {
        $_SESSION['flash_error'] = "이메일 또는 비밀번호가 올바르지 않습니다.";
        header('Location: /?page=login');
        exit;
      }

      session_regenerate_id(true);
      unset($_SESSION['old_email']);

      $_SESSION['user'] = [
        'id' => (int)$user['id'],
        'name' => (string)$user['name'],
        'email' => (string)$user['email'],
        'role' => (string)$user['role'],
      ];

      header('Location: /');
      exit;
    } catch (Throwable $e) {
      $_SESSION['flash_error'] = "로그인 처리 중 오류가 발생했습니다.";
      header('Location: /?page=login');
      exit;
    }
  }

  public function logout(): void
  {
    $_SESSION = [];

    if (ini_get('session.use_cookies')) {
      $params = session_get_cookie_params();
      setcookie(
        session_name(),
        '',
        time() - 42000,
        $params['path'],
        $params['domain'],
        (bool)$params['secure'],
        (bool)$params['httponly']
      );
    }

    session_destroy();
    header('Location: /');
    exit;
  }
}
