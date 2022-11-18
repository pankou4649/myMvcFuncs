<?php

    echo "11111111111111111111111";
    // 1.言語、文字コードを指定
    mb_language("Ja");
    mb_internal_encoding("UTF-8");
    
    // 送信先、件名、本文を変数に格納
    $mailto = "kpan@ontra.jp";
    $subject = "パンコウテストメールです。";
    $content = "こんにちは。";
    
    // 2.差出人を日本語表示
    $mailfrom="From:" .mb_encode_mimeheader("潘こうテストメールです。") ."<example@example.com>";
    
    // 3.上記（送信先、件名、本文、差出人）を日本語でメール送信実行
   $mailrtn= mb_send_mail($mailto, $subject, $content, $mailfrom);
   echo "---333333333333333333--------送信の返す値".$mailrtn;
	if($mailrtn){
		return "success";
	}else{
		return  "failed";
	}
 