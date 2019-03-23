easyphp-curl
============

easy curl wrapper for php
example:

include 'easycurl.php';
$index=1;
$book_num=1; // put your book number here
$cookie=FALSE; // put your cookie here
$headers=array("Cookie: ".$cookie,"Accept: image/webp,image/apng,image/*,*/*;q=0.8","Accept-Encoding: gzip, deflate, br","Accept-Language: en-US,en;q=0.9,ru;q=0.8","Connection: keep-alive", "Host: www.litres.ru");
while ($index<12345) // put your last page number here
{
    $url="https://www.litres.ru/pages/get_pdf_page/?file=".$book_num."&page=".$index."&rt=w1900&ft=gif";
    $host_reply=askhost($url,array("headers"=>$headers,"debug"=>TRUE,"insecure"=> TRUE));
    echo $host_reply["httpcode"];
    echo $host_reply["d"];
    // uncomment line below if you want to save original page (just in case)
    //file_put_contents(sprintf("%003s",$index).".gif", $host_reply["data"]); 
    $image = @imagecreatefromstring($host_reply["data"]);
      if ($image==FALSE) {       
      continue;  // repeat this page, it looks like broken
      }
       $width=800;
       $new=imagescale($image, $width, -1);
    imagejpeg($new,sprintf("%003s",$index).".jpg", 75); // save scaled page
    $index++;
}
