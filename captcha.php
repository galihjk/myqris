<?php
include("init.php");

// Membuat string acak
$permitted_chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ123456789';
function generate_string($input, $strength = 5) {
    $input_length = strlen($input);
    $random_string = '';
    for ($i = 0; $i < $strength; $i++) {
        $random_character = $input[mt_rand(0, $input_length - 1)];
        $random_string .= $random_character;
    }
    return $random_string;
}

// Simpan string acak ke sesi
$_SESSION['captcha_text'] = generate_string($permitted_chars, 6);

// Buat gambar
$image = imagecreatetruecolor(200, 50);
$background_color = imagecolorallocate($image, 255, 255, 255);
$text_color = imagecolorallocate($image, 0, 0, 0);
$line_color = imagecolorallocate($image, 64, 64, 64);
$pixel_color = imagecolorallocate($image, 0, 0, 255);

// Isi latar belakang
imagefilledrectangle($image, 0, 0, 200, 50, $background_color);

// Tambahkan teks
$font_path = 'assets/captcha.ttf'; // Path ke font

// Tambahkan teks dengan warna acak yang tidak terlalu terang
for ($i = 0; $i < strlen($_SESSION['captcha_text']); $i++) {
    $text_color = imagecolorallocate($image, mt_rand(0, 200), mt_rand(0, 200), mt_rand(0, 200));
    imagettftext($image, 20, mt_rand(-15, 15), 30 + ($i * 25), 40, $text_color, $font_path, $_SESSION['captcha_text'][$i]);
}

// Tambahkan beberapa garis acak untuk membuatnya lebih sulit
for ($i = 0; $i < 4; $i++) {
    imageline($image, 0, mt_rand() % 50, 200, mt_rand() % 50, $line_color);
}

// Tambahkan beberapa piksel acak
for ($i = 0; $i < 600; $i++) {
    imagesetpixel($image, mt_rand() % 200, mt_rand() % 50, $pixel_color);
}

// Kirim gambar ke browser
header('Content-type: image/png');
imagepng($image);
imagedestroy($image);
?>
