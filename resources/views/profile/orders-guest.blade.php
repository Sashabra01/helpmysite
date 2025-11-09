<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>История заказов</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; text-align: center; }
        .guest-form { max-width: 400px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 8px; }
        .guest-form input { width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #ddd; border-radius: 4px; }
        .guest-form button { background: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; }
    </style>
</head>
<body>
    <h1>📋 История ваших заказов</h1>
    <p>Введите ваш email чтобы посмотреть историю заказов</p>
    
    <div class="guest-form">
        <form method="POST" action="{{ route('profile.set-session') }}">
            @csrf
            <input type="email" name="email" placeholder="Ваш email" required>
            <button type="submit">Показать заказы</button>
        </form>
    </div>
    
    <br>
    <a href="/catalog">← Вернуться в каталог</a>
</body>
</html>