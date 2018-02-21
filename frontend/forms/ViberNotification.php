<?php
/**
 * Created by PhpStorm.
 * User: mike
 * Date: 15.02.2018
 * Time: 12:48
 */

namespace frontend\forms;

use common\components\Viber;
use common\entities\ContactCollection;
use common\entities\MessageContactCollection;
use common\entities\mongo\Phone;
use common\entities\ViberMessage;
use yii\db\Exception;
use yii\web\UploadedFile;
use Yii;
use yii\base\Model;

/**
 * Class ViberNotification
 * разбирает сообщение от вайбера
 *
 * @package frontend\forms
 */
class ViberNotification extends Model
{
    public $p_transaction_id;

    public $sending_method;

    public $msg_id;

    public $type;

    public $status;


    public function rules()
    {
        return [
            [['p_transaction_id','sending_method', 'msg_id', 'type' ], 'required'],
            [['p_transaction_id' ], 'integer'],
            [['sending_method' ], 'in', 'range'=>[ 'viber']],
            ['type', 'in','range'=>['seen','delivered','delivery']],
            ['status', 'string'],
            ['msg_id','string']
        ];
    }

}