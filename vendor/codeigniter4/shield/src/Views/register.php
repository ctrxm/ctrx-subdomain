<!DOCTYPE html>
<html lang="id" class="dark">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Register - CTRX</title>

  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.css" rel="stylesheet" />
  <link href="https://rsms.me/inter/inter.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <style>
    body {
      font-family: 'Inter', sans-serif;
      background-color: #0a0a0a;
      color: #f72585;
      min-height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      padding: 1rem;
      overflow-x: hidden;
    }

    .container {
      background: linear-gradient(135deg, #1a0a1a 0%, #310531 100%);
      border: 2px solid #f72585cc;
      border-radius: 20px;
      padding: 2.5rem 2rem;
      width: 100%;
      max-width: 420px;
      box-shadow:
        0 0 25px #f72585cc,
        inset 0 0 15px #b5179ecc;
      backdrop-filter: blur(10px);
    }

    h2 {
      text-align: center;
      color: #f72585;
      font-size: 2rem;
      margin-bottom: 0.3rem;
      letter-spacing: 0.15em;
      text-transform: uppercase;
    }

    p.sub {
      text-align: center;
      color: #bb86fcaa;
      font-size: 0.85rem;
      margin-bottom: 1.8rem;
      font-style: italic;
    }

    .alert {
      background: #7f0d48cc;
      color: #ff94b0;
      padding: 0.75rem 1rem;
      border-radius: 10px;
      margin-bottom: 1rem;
      font-size: 0.9rem;
      font-weight: 600;
    }

    form > div {
      margin-bottom: 1.25rem;
    }

    label {
      display: block;
      margin-bottom: 0.3rem;
      font-weight: 700;
      user-select: none;
      color: #f72585dd;
    }

    input[type="email"],
    input[type="text"],
    input[type="password"] {
      width: 100%;
      background: #1a0a1a;
      border: 1.8px solid #b5179e;
      border-radius: 12px;
      padding: 0.65rem 1rem;
      color: #f72585;
      font-weight: 600;
      font-size: 1rem;
      outline: none;
      transition: border-color 0.3s ease, box-shadow 0.3s ease;
    }

    input::placeholder {
      color: #f72585aa;
    }

    input:focus {
      border-color: #f72585;
      box-shadow: 0 0 15px #f72585cc;
      background: #310531;
    }

    button[type="submit"] {
      width: 100%;
      padding: 0.875rem;
      background: linear-gradient(45deg, #b5179e, #f72585);
      border: none;
      color: #fff;
      font-weight: 900;
      text-transform: uppercase;
      border-radius: 15px;
      cursor: pointer;
      box-shadow: 0 0 15px #f72585cc;
      transition: background 0.3s ease;
    }

    button[type="submit"]:hover {
      background: linear-gradient(45deg, #f72585, #b5179e);
      box-shadow: 0 0 30px #f72585dd;
    }

    p.link {
      margin-top: 1.2rem;
      text-align: center;
      color: #bb86fcaa;
      font-size: 0.9rem;
      user-select: none;
    }

    p.link a {
      color: #f72585;
      text-decoration: underline;
      transition: color 0.3s ease;
    }

    p.link a:hover {
      color: #ff64ac;
    }
  </style>
</head>
<body>

  <div class="container">
    <h2><?= lang('Auth.register') ?></h2>
    <p class="sub">Join CTRX CORP · Secure Access</p>

    <?php if (session('error') !== null) : ?>
      <div class="alert"><?= session('error') ?></div>
    <?php elseif (session('errors') !== null) : ?>
      <div class="alert">
        <?php if (is_array(session('errors'))) : ?>
          <?php foreach (session('errors') as $error) : ?>
            <?= $error ?><br>
          <?php endforeach ?>
        <?php else : ?>
          <?= session('errors') ?>
        <?php endif ?>
      </div>
    <?php endif ?>

    <form action="<?= url_to('register') ?>" method="post" autocomplete="off">
      <?= csrf_field() ?>

      <div>
        <label for="email"><i class="fas fa-envelope"></i> <?= lang('Auth.email') ?></label>
        <input type="email" id="email" name="email" placeholder="<?= lang('Auth.email') ?>" required value="<?= old('email') ?>" />
      </div>

      <div>
        <label for="username"><i class="fas fa-user"></i> <?= lang('Auth.username') ?></label>
        <input type="text" id="username" name="username" placeholder="<?= lang('Auth.username') ?>" required value="<?= old('username') ?>" />
      </div>

      <div>
        <label for="password"><i class="fas fa-lock"></i> <?= lang('Auth.password') ?></label>
        <input type="password" id="password" name="password" placeholder="<?= lang('Auth.password') ?>" required />
      </div>

      <div>
        <label for="password_confirm"><i class="fas fa-lock"></i> <?= lang('Auth.passwordConfirm') ?></label>
        <input type="password" id="password_confirm" name="password_confirm" placeholder="<?= lang('Auth.passwordConfirm') ?>" required />
      </div>

      <button type="submit"><?= lang('Auth.register') ?></button>
    </form>

    <p class="link">
      <?= lang('Auth.haveAccount') ?> <a href="<?= url_to('login') ?>"><?= lang('Auth.login') ?></a>
    </p>
  </div>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.js"></script>
</body>
</html>
