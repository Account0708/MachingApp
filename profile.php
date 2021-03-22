<?php
session_start();
require('./dbconnect.php');

if (isset($_SESSION['id']) && $_SESSION['time'] + 3600 > time()) {
    $_SESSION['time'] = time();

    $members = $db->prepare('SELECT * FROM members WHERE id=?');
    $members->execute(array(
        $_SESSION['id']
    ));
    $member = $members->fetch();
} else {
    header('Location: login.php');
    exit();
}

$members = $db->prepare('SELECT * FROM members WHERE id=?');
$members->execute(array(
    $_SESSION['id']
));
$member = $members->fetch();

$birthday = '';
if ($member['birthday'] == 0) {
    $birthday = '入力されていません';
} else {
    $birthday = $member['birthday'];
}

$gender = '';
if ($member['gender'] == 0) {
    $gender = '回答無し';
} else if ($member['gender'] == 1) {
    $gender = '男';
} else if ($member['gender'] == 2) {
    $gender = '女';
} else if ($member['gender'] == 9) {
    $gender = 'その他';
}
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>ひとこと掲示板</title>

    <link rel="stylesheet" href="style.css" />
</head>

<body>
    <div id="wrap">
        <div id="head">
            <h1><?php echo htmlspecialchars($member['name'], ENT_QUOTES); ?>さんのプロフィール</h1>
        </div>
        <div id="content">
            <div class=" flex jc_end">
                <div style="text-align: right" class="mlr10"><a href="index.php">投稿ページ</a></div>
                <div style="text-align: right" class="mlr10"><a href="logout.php">ログアウト</a></div>
            </div>
            <dl>
                <dt>ニックネーム</dt>
                <dd>
                    <?php echo htmlspecialchars($member['name'], ENT_QUOTES); ?>

                </dd>
                <dt>メールアドレス</dt>
                <dd>
                    <?php echo htmlspecialchars($member['email'], ENT_QUOTES); ?>
                </dd>
                <dt>パスワード</dt>
                <dd>
                    【表示されません】
                </dd>
                <dt>写真</dt>
                <dd>
                    <?php if ($member['picture'] !== '') : ?>
                        <img width="250px" height="auto" src="member_picture/<?php echo htmlspecialchars($member['picture'], ENT_QUOTES); ?>" alt="">
                    <?php endif; ?>
                </dd>
                <dt>生年月日</dt>
                <dd>
                    <?php echo htmlspecialchars($birthday, ENT_QUOTES); ?>
                </dd>
                <dt>性別</dt>
                <dd>
                    <?php echo htmlspecialchars($gender, ENT_QUOTES); ?>
                </dd>
            </dl>

            <div>
                <p>
                    <a href="./setting/profile.php">
                        編集する
                    </a>
                </p>
            </div>


            <div class="questions_btn">
                <a href="questions.php"></a>
            </div>
        </div>
    </div>
</body>

</html>