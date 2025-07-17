<?php

namespace Test\Controller;

use Test\Model\User;
use Test\Model\Log;

class AuthController
{
    private $db;
    private $userModel;
    private $logModel;

    public function __construct($db)
{
    $this->db = $db;
    $this->userModel = new User($db);
// Variante mit manuellem Pfad-Check
// Variante mit realpath (empfohlen)
$logCsvPath = realpath(__DIR__ . '/../../../data/log.csv');
if ($logCsvPath === false) {
    throw new \Exception("Log-Datei nicht gefunden!");
}
$this->logModel = new \Test\Model\Log($logCsvPath);



    session_start();
}


    public function handleRequest()
    {
        // Logout
        if (isset($_GET['action']) && $_GET['action'] === 'logout') {
            $this->logout();
            return;
        }

        // Bereits eingeloggt
        if (isset($_SESSION['username'])) {
            $this->showWelcomePage();
            return;
        }

        // Loginversuch per POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleLogin();
        } else {
            $this->showLoginForm();
        }
    }

    private function handleLogin()
    {
        $username = trim($_POST['username'] ?? '');
        $password = trim($_POST['password'] ?? '');

        if (empty($username) || empty($password)) {
            $this->showLoginForm('Bitte alle Felder ausfüllen.');
            return;
        }

        $result = $this->userModel->authenticate($username, $password);

        switch ($result['status']) {
            case 'success':
                $_SESSION['username'] = $username;
                $_SESSION['lastlogin'] = $result['user']['lastlogin'];
                $this->logModel->write($username, 'Login erfolgreich');
                $this->showWelcomePage();
                break;

            case 'blocked':
                $this->logModel->write($username, 'Loginversuch bei gesperrtem Benutzer');
                $this->showLoginForm('Ihr Konto ist gesperrt.');
                break;

            case 'invalid':
                $this->logModel->write($username, 'Login fehlgeschlagen');
                $user = $this->userModel->findByUsername($username);
                if ($user && $user['blocked'] === '1') {
                    $this->logModel->write($username, 'Benutzer wurde gesperrt wegen zu vieler Fehlversuche beim Login');
                }
                $this->showLoginForm('Benutzername oder Passwort falsch.');
                break;
        }
    }

    private function logout()
    {
        if (isset($_SESSION['username'])) {
            $this->logModel->write($_SESSION['username'], 'Logout durchgeführt');
        }
        session_destroy();
        header('Location: /');
        exit;
    }

    private function showLoginForm($error = '')
    {
        $viewPath = realpath(__DIR__ . '/../View/login.php');
        if ($viewPath === false) {
            throw new \Exception('View-Datei nicht gefunden: login.php');
        }
        include $viewPath;
    }

    private function showWelcomePage()
    {
        $username = $_SESSION['username'];
        $lastlogin = $_SESSION['lastlogin'];

        $logEntries = $this->logModel->getLogsByUsername($username);

        $viewPath = realpath(__DIR__ . '/../View/welcome.php');
        if ($viewPath === false) {
            throw new \Exception('View-Datei nicht gefunden: welcome.php');
        }
        include $viewPath;
    }
}
