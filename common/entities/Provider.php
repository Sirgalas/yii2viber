<?php
/**
 * Created by PhpStorm.
 * User: mike
 * Date: 02.03.2018
 * Time: 21:04
 */

namespace common\entities;

class Provider
{
    public static function getProviders()
    {
        return ['infoBup', 'smsonline'];
    }

    public static function getAlphaNames($provider)
    {
        if ($provider == 'infoBup') {
            return [
                'IBViber' => 'IBViber',
            ];
        }

        if ($provider == 'smsonline') {
            return [
                'TEST' => 'TEST',
                'Clickbonus' => 'Бонус',
                'Promo1' => 'Promo',
                'deliverydel' => 'Delivery',
                'InfoDostavk' => 'Dostavka',
                'EXPRESSS' => 'EXPRESS',
                'SHOPSHOP' => 'SHOP',
                'SMSfeedback' => 'Feedback',
                'sushilot' => 'Sushi',
                'Taxis' => 'Taxi',
                'Klinika' => 'Klinika',
                'Bazakvartir' => 'Недвижимость',
                'FastFitnes' => 'Фитнес',
                'ChatTest' => 'ChatTest',
                'Documents' => 'Documents',
                'AUTO' => 'AUTO',
            ];
        }
    }
}