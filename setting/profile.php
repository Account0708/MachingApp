<?php
session_start();
require('../dbconnect.php');

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

// if(!empty($member['gender'])) {
//     $gender = ;
// }

if (isset($_POST['ok'])) {



    if ($_POST['name'] == '') {
        $_POST['name'] = htmlspecialchars($member['name'], ENT_QUOTES);
    }
    if ($_POST['email'] == '') {
        $_POST['email'] = htmlspecialchars($member['email'], ENT_QUOTES);
    }
    $fileName = $_FILES['image']['name'];
    if (!empty($fileName)) {
        $ext = substr($fileName, -3);
        if ($ext != 'jpg' && $ext != 'gif' && $ext != 'png') {
            $error['image'] = 'type';
        }
    }
    if ($_POST['birthday'] == '') {
        $_POST['birthday'] =  $member['birthday'];
    }
    if ($_POST['gender'] == '') {
        $_POST['gender'] = $member['gender'];
    }

    if (empty($error)) {

        $image = date('YmdHis') . $_FILES['image']['name'];
        move_uploaded_file($_FILES['image']['tmp_name'], '../member_picture/' . $image);

        $_SESSION['setting'] = $_POST;
        $_SESSION['setting']['image'] = $image;
    }

    $statement = $db->prepare('UPDATE members SET name=?, email=?, picture=?, birthday=?, gender=?');
    $statement->execute(array(
        $_SESSION['setting']['name'],
        $_SESSION['setting']['email'],
        $_SESSION['setting']['image'],
        $_SESSION['setting']['birthday'],
        $_SESSION['setting']['gender']
    ));
    unset($_SESSION['setting']);

    header('Location: ../profile.php');
    exit();
}
// if (!empty($_POST)) {

//     if ($_POST['name'] == '') {
//         $_POST['name'] = $member['name'];
//     }
//     if ($_POST['email'] == '') {
//         $_POST['email'] = $member['email'];
//     }
//     if (strlen($_POST['password']) < 4) {
//         $error['password'] = 'length';
//     }
//     if ($_POST['password'] == '') {
//         $_POST['password'] = $member['email'];
//     }
//     $fileName = $_FILES['image']['name'];
//     if (!empty($fileName)) {
//         $ext = substr($fileName, -3);
//         if ($ext != 'jpg' && $ext != 'gif' && $ext != 'png') {
//             $error['image'] = 'type';
//         }
//     }
//     if ($_POST['birthday'] == '') {
//         $_POST['birthday'] = $member['birthday'];
//     }
//     if ($_POST['gender'] == '') {
//         $_POST['gender'] = $member['gender'];
//     }

//アカウントの重複チェック
// if (empty($error)) {
//     $member = $db->prepare('SELECT COUNT(*) AS cnt FROM members WHERE email=?');
//     $member->execute(array($_POST['email']));
//     $record = $member->fetch();
//     if ($record['cnt'] > 0) {
//         $error['email'] = 'duplicate';
//     }
// }

//     if (empty($error)) {
//         $image = date('YmdHis') . $_FILES['image']['name'];
//         move_uploaded_file($_FILES['image']['tmp_name'], '../member_picture/' . $image);
//         $_SESSION['setting'] = $_POST;
//         $_SESSION['setting']['image'] = $image;
//     }

//     $statement = $db->prepare('UPDATE members SET name=?, email=?, password=?, picture=?, birthday=?, gender=?');
//     $statement->execute(array(
//         $_SESSION['setting']['name'],
//         $_SESSION['setting']['email'],
//         sha1($_SESSION['setting']['password']),
//         $_SESSION['setting']['image'],
//         $_SESSION['setting']['birthday'],
//         $_SESSION['setting']['gender']
//     ));
//     unset($_SESSION['setting']);

//     header('Location: ../profile.php');
//     exit();
// }
// if ($_REQUEST['action'] == 'rewrite' && isset($_SESSION['setting'])) {
//     $_POST = $_SESSION['setting'];
// }
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>編集画面｜ひとこと掲示板</title>

    <link rel="stylesheet" href="../style.css" />
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
            <form action="" method="post" enctype="multipart/form-data">
                <dl>
                    <dt>ニックネーム</dt>
                    <dd>
                        <!-- <?php echo htmlspecialchars($member['name'], ENT_QUOTES); ?> -->
                        <input type="text" name="name" size="35" maxlength="255" value="<?php print(htmlspecialchars($member['name'], ENT_QUOTES)); ?>" />
                    </dd>
                    <dt>メールアドレス</dt>
                    <dd>
                        <!-- <?php echo htmlspecialchars($member['email'], ENT_QUOTES); ?> -->
                        <input type="text" name="email" size="35" maxlength="255" value="<?php print(htmlspecialchars($member['email'], ENT_QUOTES)); ?>" />
                    </dd>
                    <dt>パスワード</dt>
                    <dd>
                        【表示されません】
                        <!-- <input type="text" name="password" size="35" maxlength="255" value="【表示されません】" /> -->
                    </dd>
                    <dt>写真</dt>
                    <dd>
                        <?php if ($member['picture'] !== '') : ?>
                            <img width="250px" height="auto" src="../member_picture/<?php echo htmlspecialchars($member['picture'], ENT_QUOTES); ?>" alt="">

                            <?php if ($error['image'] === 'type') : ?>
                                <p class="error">*写真などは「.gif」または「.jpg」「.png」の画像を指定してください</p>
                            <?php endif; ?>
                            <?php if (!empty($error)) : ?>
                                <p class="error">*恐れ入りますが、画像を改めて指定してください</p>
                            <?php endif; ?>
                        <?php endif; ?>
                        <br>
                        <input type="file" name="image" size="35" value="" />
                    </dd>
                    <dt>生年月日</dt>
                    <dd>
                        <!-- <?php echo htmlspecialchars($birthday, ENT_QUOTES); ?> -->
                        <input type="date" name="birthday" min="1900-04-01" value="<?php print(htmlspecialchars($member['birthday'], ENT_QUOTES)); ?>" />
                    </dd>
                    <dt>性別</dt>
                    <dd>
                        <!-- <?php echo htmlspecialchars($gender, ENT_QUOTES); ?> -->
                        <select name="gender" value="<?php $member['gender']; ?>">
                            <option value="0" <?php if ($member['gender'] == 0) {
                                                    echo 'selected';
                                                } ?>>回答無し</option>
                            <option value="1" <?php if ($member['gender'] == 1) {
                                                    echo 'selected';
                                                } ?>>男</option>
                            <option value="2" <?php if ($member['gender'] == 2) {
                                                    echo 'selected';
                                                } ?>>女</option>
                            <option value="9" <?php if ($member['gender'] == 9) {
                                                    echo 'selected';
                                                } ?>>その他</option>
                        </select>
                    </dd>
                </dl>

                <div class="">
                    <p>
                        <a href="../profile.php">
                            戻る
                        </a>
                    </p>
                    <p>
                        <input type="submit" name="ok" value="確定する">
                    </p>
                </div>
            </form>

            <div class="questions_btn">
                <a href="questions.php"></a>
            </div>
        </div>
    </div>
</body>

</html>