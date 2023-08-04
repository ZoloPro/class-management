<?php

namespace App\Helpers;

class EmailHelpers
{
    public static function hideEmailName($email)
    {
        // Tìm vị trí của ký tự @ trong địa chỉ email
        $atPosition = strpos($email, '@');

        // Nếu không tìm thấy @, hoặc @ ở vị trí đầu tiên hoặc cuối cùng, không cần che dấu
        if ($atPosition === false || $atPosition === 0 || $atPosition === strlen($email) - 1) {
            return $email;
        }

        // Lấy phần trước và sau ký tự @
        $beforeAt = substr($email, 0, $atPosition);
        $afterAt = substr($email, $atPosition + 1);

        // Lấy phần đầu của tên (phần trước ký tự @) và che dấu bằng dấu *
        $namePart = substr($beforeAt, 0, 2); // Chừa lại 2 ký tự của phần đầu name
        $hiddenPart = str_repeat('*', strlen($beforeAt) - 2) . '@';

        // Kết hợp phần đã che dấu với phần sau ký tự @ để tạo email che dấu
        $hiddenEmail = $namePart . $hiddenPart . $afterAt;

        return $hiddenEmail;
    }
}
