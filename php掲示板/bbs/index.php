<?php
session_start();
require('library.php');

if(isset($_SESSION['id']) && isset($_SESSION['name'])){
    $id = $_SESSION['id'];
    $name = $_SESSION['name'];
}else{
    header('Location:login.php');
    exit();
}

$db = dbconnect();
//メッセージの投稿
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $message = filter_input(INPUT_POST,'message',FILTER_DEFAULT);
    $stmt = $db->prepare('insert into posts (message,member_id) values(?,?)');
    if(!$stmt){
        die($db->error);
    }
    $stmt -> bind_param('si',$message,$id);
    $success = $stmt->execute();
    if(!$success){
        die($db->error);
    }

    header('Location:index.php');
    exit();
}

?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>掲示板</title>

    <link rel="stylesheet" href="style3.css"/>
</head>

<body>
<div id="wrap">
    <div id="head">
        <h1>掲示板</h1>
    </div>
    <div id="content">
        <div class = "logout"><a href="logout.php">ログアウト</a></div>
        <form action="" method="post">
            <dl>
                <dt><?php echo h($name); ?>さん、メッセージをどうぞ</dt>
                <dd>
                    <textarea name="message" cols="50" rows="5"></textarea>
                </dd>
            </dl>
            <div>
                <p>
                    <input type="submit" value="投稿する"/>
                </p>
            </div>
        </form>
        <?php 
        $stmt = $db->prepare('select p.id,p.member_id,p.message,p.created,m.name,m.picture from posts p,members m 
        where m.id = p.member_id order by id desc ');
        if(!$stmt){
            die($db->error);
        }
        $success = $stmt->execute();
        if(!$success){
            die($db->error);
        }
        $stmt->bind_result($id,$member_id,$message,
        $created,$name,$picture);
        while($stmt->fetch()):
        ?>
        <div class="msg">
            <?php if($picture):?>
                <img src="member_picture/<?php echo h($picture);?>" alt=""/>
            <?php endif;?>
            <p>
                <?php echo h($message);?>
                <span class="name">
                    (<a href = "view.php?id= <?php echo h($id);?>" style = "text-decoration: none; ">
                        <?php echo h($name);?>
                    </a>)
                </span>
            </p>
            <p class="day">
                <?php echo h($created);?>
                <?php if($_SESSION['id'] === $member_id):?>
                [<a href="delete.php?id=<?php echo h($id);?>" style="color:#F33;">削除</a>]
            <?php endif;?>
            </p>
        </div>
        <?php endwhile;?>
    </div>
</div>
</body>

</html>