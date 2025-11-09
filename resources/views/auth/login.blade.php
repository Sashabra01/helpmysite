<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход в систему</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; text-align: center; }
        .auth-form { max-width: 400px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 8px; }
        .auth-form input { width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #ddd; border-radius: 4px; }
        .auth-form button { background: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; width: 100%; }
        .demo-info { background: #fff3cd; border: 1px solid #ffeaa7; padding: 15px; border-radius: 5px; margin: 20px 0; }
    </style>
</head>
<body>
    <h1>🔐 Вход в систему</h1>
    
    <div class="demo-info">
        <strong>Демо-версия:</strong> Для тестирования просто нажмите "Войти"
    </div>
    
    <div class="auth-form">
        <form method="POST" action="#">
            @csrf
            <input type="email" name="email" placeholder="Email" value="demo@example.com" readonly>
            <input type="password" name="password" placeholder="Пароль" value="demo123" readonly>
            <button type="button" onclick="simulateLogin()">Войти</button>
        </form>
    </div>
    
    <br>
    <p>Нет аккаунта? <a href="{{ route('register') }}">Зарегистрироваться</a></p>
    <a href="/catalog">← Вернуться в каталог</a>

    <script>
        function simulateLogin() {
            // В демо-версии просто редиректим
            alert('Демо-вход выполнен!');
            window.location.href = '/profile/orders';
        }
    </script>
</body>
</html>