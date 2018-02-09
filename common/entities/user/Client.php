<?php
/**
 * Created by PhpStorm.
 * User: mike
 * Date: 09.02.2018
 * Time: 8:14
 */

namespace common\entities\user;

use Helper\Extended;

class Client extends User
{
    /**
     * @param string $type
     * @return \yii\db\ActiveQuery
     */
    public static function getMyClients($type='all'){
        $ids = getClildList();
        if ($ids === -1){
            return [];
        }
        return self::find()->andWhere(['in','id', 'ids'])->asArray()->all();
    }


}