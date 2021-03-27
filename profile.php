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

if (!empty($_POST)) {
    if ($_POST['message'] !== '') {
        $message = $db->prepare('INSERT INTO posts SET member_id=?, message=?, reply_message_id=?, created=NOW()');
        $message->execute(array(
            $member['id'],
            $_POST['message'],
            $_POST['reply_post_id']
        ));

        header('Location: index.php');
        exit();
    }
}

// $members = $db->prepare('SELECT * FROM members WHERE id=?');
// $members->execute(array(
//     $_SESSION['id']
// ));
// $member = $members->fetch();

if ((int)$_GET["id"] > 0) {
    $page = $_GET["id"];

    if (isset($page) && $_SESSION['time'] + 3600 > time()) {
        $_SESSION['time'] = time();

        $other_members = $db->prepare('SELECT * FROM members WHERE id=?');
        $other_members->execute(array(
            (int)$page
        ));
        $other_member = $other_members->fetch();
    } else {
        header('Location: login.php');
        exit();
    }
} else {
    $page = $member['id'];
}

// birthday

$birthday = '';
if ($member['birthday'] == 0) {
    $birthday = '入力されていません';
} else {
    $birthday = $member['birthday'];
}

$other_birthday = '';
if ($other_member['birthday'] == 0) {
    $other_birthday = '入力されていません';
} else {
    $other_birthday = $other_member['birthday'];
}

// gender

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

$other_gender = '';
if ($other_member['gender'] == 0) {
    $other_gender = '回答無し';
} else if ($other_member['gender'] == 1) {
    $other_gender = '男';
} else if ($other_member['gender'] == 2) {
    $other_gender = '女';
} else if ($other_member['gender'] == 9) {
    $other_gender = 'その他';
}

// hobby

if ($page == $member['id']) {

    $hobby = '';
    if ($member['hobby'] == 0) {
        $hobby = '回答無し';
    } else if ($member['hobby'] == 1) {
        $hobby = '映画鑑賞';
    } else if ($member['hobby'] == 2) {
        $hobby = 'ゲーム';
    } else if ($member['hobby'] == 3) {
        $hobby = '運動';
    }
} else {

    $other_hobby = '';
    if ($other_member['hobby'] == 0) {
        $other_hobby = '回答無し';
    } else if ($other_member['hobby'] == 1) {
        $other_hobby = '映画鑑賞';
    } else if ($other_member['hobby'] == 2) {
        $other_hobby = 'ゲーム';
    } else if ($other_member['hobby'] == 3) {
        $other_hobby = '運動';
    }
}


// address

if ($page == $member['id']) {

    if ($member['address'] == '') {
        $member['address'] = '回答無し';
    }
} else {

    if ($other_member['address'] == '') {
        $other_member['address'] = '回答無し';
    }
}

// occupation

if ($page == $member['id']) {

    if ($member['occupation'] == '') {
        $member['occupation'] = '回答無し';
    }
} else {

    if ($other_member['occupation'] == '') {
        $other_member['occupation'] = '回答無し';
    }
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
            <h1><?php
                if ($page == $member['id']) {
                    echo htmlspecialchars($member['name'], ENT_QUOTES);
                } else {
                    echo htmlspecialchars($other_member['name'], ENT_QUOTES);
                }
                ?>さんのプロフィール</h1>
        </div>
        <div id="content">
            <div class=" flex jc_end">
                <div style="text-align: right" class="mlr10"><a href="index.php">投稿ページ</a></div>
                <div style="text-align: right" class="mlr10"><a href="logout.php">ログアウト</a></div>
            </div>
            <dl>
                <dt>ニックネーム</dt>
                <dd>
                    <?php
                    if ($page == $member['id']) {
                        echo htmlspecialchars($member['name'], ENT_QUOTES);
                    } else {
                        echo htmlspecialchars($other_member['name'], ENT_QUOTES);
                    }
                    ?>

                </dd>
                <dt>メールアドレス</dt>
                <dd>
                    <?php
                    if ($page == $member['id']) {
                        echo htmlspecialchars($member['email'], ENT_QUOTES);
                    } else {
                        // echo htmlspecialchars($other_member['email'], ENT_QUOTES);
                        echo '【表示されません】';
                    }
                    ?>
                </dd>
                <dt>パスワード</dt>
                <dd>
                    【表示されません】
                </dd>
                <dt>写真</dt>
                <dd>
                    <?php if ($member['picture'] !== '') : ?>
                        <img width="250px" height="auto" src="member_picture/<?php if ($page == $member['id']) {
                                                                                    echo htmlspecialchars($member['picture'], ENT_QUOTES);
                                                                                } else {
                                                                                    echo htmlspecialchars($other_member['picture'], ENT_QUOTES);
                                                                                } ?>" alt="">
                    <?php endif; ?>
                </dd>
                <dt>生年月日</dt>
                <dd>
                    <?php
                    if ($page == $member['id']) {
                        echo htmlspecialchars($birthday, ENT_QUOTES);
                    } else {
                        echo htmlspecialchars($other_birthday, ENT_QUOTES);
                    }
                    ?>
                </dd>
                <dt>性別</dt>
                <dd>
                    <?php
                    if ($page == $member['id']) {
                        echo htmlspecialchars($gender, ENT_QUOTES);
                    } else {
                        echo htmlspecialchars($other_gender, ENT_QUOTES);
                    }
                    ?>
                </dd>
                <dt>趣味</dt>
                <dd>
                    <?php
                    if ($page == $member['id']) {
                        echo htmlspecialchars($hobby, ENT_QUOTES);
                    } else {
                        echo htmlspecialchars($other_hobby, ENT_QUOTES);
                    }
                    ?>
                </dd>
                <dt>所在地</dt>
                <dd>
                    <?php
                    if ($page == $member['id']) {
                        echo htmlspecialchars($member['address'], ENT_QUOTES);
                    } else {
                        echo htmlspecialchars($other_member['address'], ENT_QUOTES);
                    }
                    ?>
                </dd>
                <dt>職種</dt>
                <dd>
                    <?php
                    if ($page == $member['id']) {
                        echo htmlspecialchars($member['occupation'], ENT_QUOTES);
                    } else {
                        echo htmlspecialchars($other_member['occupation'], ENT_QUOTES);
                    }
                    ?>
                </dd>
            </dl>

            <div>
                <a href="./index.php" class="mlr10">
                    戻る
                </a>
                <?php if ($page == $member['id']) : ?>
                    <a href="./setting/profile.php" class="mlr10">
                        編集する
                    </a>
                <?php endif ?>
            </div>


            <div class="questions_btn">
                <a href="questions.php"></a>
            </div>
        </div>
    </div>
</body>

</html>