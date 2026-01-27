<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - Forbidden</title>
    <style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }

    .error-container {
        background: white;
        border-radius: 10px;
        padding: 40px;
        max-width: 600px;
        width: 100%;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
        text-align: center;
    }

    .error-code {
        font-size: 72px;
        font-weight: bold;
        color: #f5576c;
        margin-bottom: 20px;
    }

    .error-title {
        font-size: 24px;
        color: #333;
        margin-bottom: 15px;
    }

    .error-message {
        font-size: 16px;
        color: #666;
        margin-bottom: 30px;
        line-height: 1.6;
    }

    .btn {
        display: inline-block;
        padding: 12px 30px;
        background: #f5576c;
        color: white;
        text-decoration: none;
        border-radius: 5px;
        margin-top: 20px;
        transition: background 0.3s;
    }

    .btn:hover {
        background: #e0455a;
    }
    </style>
</head>

<body>
    <div class="error-container">
        <div class="error-code">403</div>
        <h1 class="error-title">Access Forbidden</h1>
        <p class="error-message">
            <?= htmlspecialchars($message ?? 'You do not have permission to access this resource.'); ?>
        </p>
        <a href="/" class="btn">Go to Homepage</a>
    </div>
</body>

</html>