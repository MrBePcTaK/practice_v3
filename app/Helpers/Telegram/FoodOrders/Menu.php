<?php

namespace App\Helpers\Telegram\FoodOrders;

use App\Models\Product;

class Menu
{
    public const BUSINESS_LUNCH = 'business_lunch';

    private function parseItem($itemString, $categoryPrice): object
    {
        if(str_contains($itemString, '₽')) {
            if (str_contains($itemString, '(')) {
                $name = mb_strcut(
                    $itemString,
                    0,
                    strpos($itemString, '(')
                );
                $ingredients = mb_strcut(
                    $itemString,
                    strpos($itemString, '(') + 1,
                    strpos($itemString, ')') - strpos($itemString, '(') - 1
                );
            } else {
                $name = mb_strcut(
                    $itemString,
                    0,
                    strrpos($itemString, '-')
                );
                $ingredients = '';
            }
            $price = intval(mb_strcut($itemString,strrpos($itemString, '-') + 1));
        } else {
            $name = $itemString;
            $ingredients = '';
            $price = $categoryPrice;
        }
        return (object) array(
            'name' => $name,
            'ingredients' => $ingredients,
            'price' => $price,
        );
    }

    public function parseMenu($text): bool
    {
        $filterStr = str_replace("▪️", '', $text);
        $categories = explode("\n\n", $filterStr);
        $menu = [];
        foreach ($categories as $category) {
            $items = explode("\n", $category);
            if (count($items) < 2) {
                continue;
            }

            $startWeight = strpos($items[0], '(');
            $endWeight = strpos($items[0], ')');
            $categoryName = mb_strcut($items[0], 0, $startWeight);
            $categoryWeight = mb_strcut($items[0], $startWeight + 1, $endWeight - $startWeight - 1);
            $categoryPrice = intval(mb_strcut($items[0], $endWeight + 2));

            $itemArray = [];
            for ($i = 1; $i < count($items); $i++) {
                $itemArray[] = $this->parseItem($items[$i], $categoryPrice);
            }

            $menu[] = (object) array(
                'name' => $categoryName,
                'weight' => $categoryWeight,
                'items' => $itemArray,
            );
        }

        $menuArr = [];
        foreach ($menu as $category) {
            foreach ($category->{'items'} as $item) {
                $menuArr[] = array(
                    'type' => self::BUSINESS_LUNCH,
                    'active' => true,
                    'category' => $category->{'name'},
                    'name' => $item->{'name'},
                    'ingredients' => $item->{'ingredients'},
                    'weight' => $category->{'weight'},
                    'price' => $item->{'price'},
                    'date' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                );
            }
        }

        Product::query()->insert($menuArr);

        return true;
    }
}
