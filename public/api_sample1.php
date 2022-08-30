<?php
// APIアクセスURL
$url = 'http://whisper.wisdom-guild.net/search.php?name=%E3%82%A2%E3%82%AB%E3%83%87%E3%83%9F%E3%83%BC%E3%81%AE%E5%A3%81';

// ストリームコンテキストのオプションを作成
$options = array(
    // HTTPコンテキストオプションをセット
    'http' => array(
        'method'=> 'GET',
        'header'=> 'Content-type: application/json; charset=UTF-8' //JSON形式で表示
    )
);

// ストリームコンテキストの作成
$context = stream_context_create($options);

$raw_data = file_get_contents($url, false,$context);

// debug
//var_dump($raw_data);

// json の内容を連想配列として $data に格納する
$data = json_decode($raw_data,true);
?>
<table>
    <tr>
        <th>name</th>
        <th>note</th>
    </tr>
    <?php foreach($data as $key => $value){ // 連想配列を取り出す ?>
        <?php if(is_array($value)){ // 値が配列のみループで回して表示 ?>
            <?php foreach($value as $column){ ?>
            <tr>
                <td><?php echo $column['name']; ?></td>
                <!-- <td><?php echo $column['note']; ?></td> -->
            </tr>
           <?php } ?>
        <?php } ?>
    <?php } ?>
</table>
?>
