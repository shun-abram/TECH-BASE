<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <title>Mission5_1</title>
</head>
<body>
    <p>デバッグ・レビューのお願いです。</p>
    <p>名前に氏名を入力してください。</p>
    <p>コメントは自由に入力してください。</p>
    <br>
    <!-- 入力フォーム作成 -->
    <form action="#" method="post">
        <p>　投稿フォーム　</p>
        <p>名前</p>
        <p><input type="text" name="mes_name"></p>
        <p>コメント</p>
        <p><input type="text" name="mes_comment" ></p>
        <p>パスワード</p>
        <p><input type="text" name="password" ></p>
        <p><input type="submit" value="送信する"></p>
        <p>　削除フォーム　</p>
        <p>削除番号</p>
        <p><input type="text" name="delete_num"></p>
        <p>パスワード</p>
        <p><input type="text" name="delete_password" ></p>
        <p><input type="submit" value="削除"></p>
        <p>　編集フォーム　</p>
        <p>編集番号</p>
        <p><input type="text" name="edit_num"></p>
        <p>名前</p>
        <p><input type="text" name="edit_mes_name"></p>
        <p>コメント</p>
        <p><input type="text" name="edit_mes_comment"></p>
        <p>パスワード</p>
        <p><input type="text" name="edit_password" ></p>
        <p><input type="submit" value="編集"></p>
        <p>DBフォーム</p>
        <p>DBのリセット</p>
        <p><input type="text" name="reset"></p>
        <p><input type="submit" value="リセット"></p>
    </form>

<?php
    
    //データベース接続
    $dsn = 'mysql:dbname=データベース名;host=localhost';
    $user = 'ユーザー名';
    $password = 'パスワード';
    $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));

    /* ファイル名宣言
    //$filename = "mission_3_5.txt";*/
    $part_name = array();
    $part_comment = array();
    $new_text = "";

    // 入力フォームから入力された文字をファイルに書き込む（モード）機能
    if (!empty($_POST["mes_name"]) && !empty($_POST["mes_comment"]) && !empty($_POST["password"]) ) {
        // 名前の代入
        $str_name = $_POST["mes_name"];
        // コメントの代入
        $str_comment = $_POST["mes_comment"];
        //パスワードの代入
        $password = $_POST["password"];
        // 日付の取得
        $date = date("Y/n/d H:i:s");

        $sql_name = "$str_name";
        $sql_datetime = "$date";
        $sql_comment = "$str_comment";
        $sql_password ="$password";
        

        $sql = "INSERT INTO tbtest (name,datetime,comment,password) VALUES (:name, :datetime, :comment, :password)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':name', $sql_name, PDO::PARAM_STR);
        $stmt->bindParam(':datetime', $sql_datetime, PDO::PARAM_STR);
        $stmt->bindParam(':comment', $sql_comment, PDO::PARAM_STR);
        $stmt->bindParam(':password', $sql_password, PDO::PARAM_STR);
        $stmt->execute();
        echo "書き込み成功！<br>";
    } 
    elseif (!empty($_POST["delete_num"]) && !empty($_POST["delete_password"]) && !empty($_POST["delete_password"]) ) {
        $delete_password = $_POST["delete_password"];
        $miss = false;
        // 削除対象番号の代入
        $delete_num = $_POST["delete_num"];

        $sql = 'SELECT * FROM tbtest';
        $stmt = $pdo->query($sql);
        $results = $stmt->fetchAll();
        foreach ($results as $row){
            if($delete_password !== $row["password"]){
                $miss = true;
            }
            else{
                $id = $delete_num;
                $sql = 'delete from tbtest where id=:id';
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $stmt->execute();
                $miss = false;
            }
        }
        if($miss == true){
            echo "パスワードが間違っています！<br>";
            
        }
        else{
            echo "削除成功！<br>";
            
        }
    }
    
    
    elseif (!empty($_POST["edit_num"]) && !empty($_POST["edit_mes_name"]) && !empty($_POST["edit_mes_comment"]) && !empty($_POST["edit_password"])) {
        // 編集対象番号の代入
        $edit_num = $_POST["edit_num"];
        $edit_name = $_POST["edit_mes_name"]; 
        $edit_comment = $_POST["edit_mes_comment"];
        $edit_password = $_POST["edit_password"];
        // 日付の取得
        $date = date("Y/n/d H:i:s");
        $miss = false;
         //DBのデータレコードを編集する
        $sql = 'SELECT * FROM tbtest';
        $stmt = $pdo->query($sql);
        $results = $stmt->fetchAll();
        //print(len($results));
        //var_dump($results);
        foreach ($results as $row){
            if($edit_password !== $row["password"]){
                $miss = true;
            }
            //name,datetime,comment,password
            else{
                $id = $edit_num; //変更する投稿番号
                //変更したいものの変数を作成
                $sql_name = "$edit_name";
                $sql_datetime = "$date";
                $sql_comment = "$edit_comment";
                
                $sql = 'UPDATE tbtest SET name=:name,datetime=:datetime,comment=:comment WHERE id=:id';
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $stmt->bindParam(':name', $sql_name, PDO::PARAM_STR);
                $stmt->bindParam(':datetime', $sql_datetime, PDO::PARAM_STR);
                $stmt->bindParam(':comment', $sql_comment, PDO::PARAM_STR);
                $stmt->execute();
                $miss = false;
            }
        }
        if($miss == true){
            echo "パスワードが間違っています！<br>";
            
        }
        else{
            echo "編集成功！<br>";
            
        }
    }
    
    //DBのリセット
    elseif(!empty($_POST["reset"])){
        $resetcall = $_POST["reset"];
        if($resetcall == "reset"){
            $sql = 'DROP TABLE tbtest';
            $stmt = $pdo->query($sql);
            $sql = "CREATE TABLE IF NOT EXISTS tbtest"
            ." ("
            . "id INT AUTO_INCREMENT PRIMARY KEY,"
            . "name CHAR(32),"
            . "comment TEXT,"
            . "datetime DATETIME,"
            . "password TEXT"
            .");";
            $stmt = $pdo->query($sql);
            echo"リセットに成功しました";

        }
        else{
            echo"resetとフォームに入れてリセットしてください。<br>";
        }
    }
    
    //DBからデータを表示
    $sql = 'SELECT * FROM tbtest';
    $stmt = $pdo->query($sql);
    $results = $stmt->fetchAll();
    foreach ($results as $row){

        echo $row['id'].$row['name'].$row['comment'].$row['datetime'].'<br>';
    }

    
?>
</body>
</html>