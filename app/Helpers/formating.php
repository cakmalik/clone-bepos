<?php

function rupiah($number)
{
    return 'Rp ' . str_replace(',00', '', number_format($number, 2, ',', '.'));
}

function MyRupiah($number)
{
    return 'Rp.' . str_replace(',00', '', number_format($number, 2, ',', '.'));
}

function numberGroup($number)
{
    return number_format($number, 0, ',', '.');
}

function numberGroupWithDec($number)
{
    $dec = isDecimal($number) ? 2 : 0;

    return number_format($number, $dec, ',', '.');
}

function isDecimal($n)
{
    return is_numeric($n) && floor($n) != $n;
}

function isActive($is)
{
    return $is ? 'AKTIF' : 'NON AKTIF';
}

function dateWithTime($date)
{
    return date('d/m/Y H:i', strtotime($date));
}

function dateStandar($date)
{
    return date('d/m/Y', strtotime($date));
}

function removeString($number)
{
    return str_replace('.', '', $number);
}

function removeRupiah($number)
{
    return str_replace('Rp ', '', $number);
}

function removeRp($number)
{
    $number = str_replace('.', '', $number);
    return str_replace('Rp ', '', $number);
}

function endOfDay($date)
{
    return date('Y-m-d 23:59:59', strtotime($date));
}

function startOfDay($date)
{
    return date('Y-m-d 00:00:00', strtotime($date));
}

function filterWord($total, $string)
{
    $string = explode(" ", $string);
    $string = array_slice($string, 0, $total);
    $string = implode(" ", $string);

    return $string;
}

function terbilang($x)
{

    $x     = abs($x);
    $angka = ["", "Satu", "Dua", "Tiga", "Empat", "Lima", "Enam", "Tujuh", "Delapan", "Sembilan", "Sepuluh", "Sebelas"];
    $temp  = "";

    if ($x < 12) {
        $temp = " " . $angka[$x];
    } else if ($x < 20) {
        $temp = terbilang($x - 10) . " Belas";
    } else if ($x < 100) {
        $temp = terbilang($x / 10) . " Puluh" . terbilang($x % 10);
    } else if ($x < 200) {
        $temp = " Seratus" . terbilang($x - 100);
    } else if ($x < 1000) {
        $temp = terbilang($x / 100) . " Ratus" . terbilang($x % 100);
    } else if ($x < 2000) {
        $temp = " Seribu" . terbilang($x - 1000);
    } else if ($x < 1000000) {
        $temp = terbilang($x / 1000) . " Ribu" . terbilang($x % 1000);
    } else if ($x < 1000000000) {
        $temp = terbilang($x / 1000000) . " Juta" . terbilang($x % 1000000);
    } else if ($x < 1000000000000) {
        $temp = terbilang($x / 1000000000) . " Milyar" . terbilang($x % 1000000000);
    }

    return $temp;
}
