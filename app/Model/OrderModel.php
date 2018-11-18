<?php

namespace App\Model;

use App\UserModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\App;
use src\Exceptions\ResourceNotFoundException;
use src\Logger\Logger;

class OrderModel extends Model
{
    use SoftDeletes;//软删除

    protected $table = 'orders';

    protected $guarded = ['id'];

    protected $dates = ['delete_at'];

    /**
     * 订单状态
     */
    const
        STATUS_NOT_RELEASED = 0,//草稿(暂时用不到)
        STATUS_RELEASED = 1,//已发布
        STATUS_RUNNING = 2,//正在服务
        STATUS_WAITING_COMMENT = 3,//服务完成等待评价
        STATUS_FINISHED = 4,//评价完成
        STATUS_CANCELED = 5;//订单取消


    /**
     * 订单类别
     */
    const
        TYPE_RUN = 0,//跑腿
        TYPE_ASK = 1,//悬赏提问
        TYPE_STUDY = 2,//学习辅导
        TYPE_TECH = 3,//技术服务
        TYPE_DAILY = 4,//生活服务
        TYPE_OTHER = 5;//其他

    /**
     * 奖励积分数量
     */
    const
        AWARD_SENDER = 1,
        AWARD_RECEIVER = 5;

    /**
     * 根据id查找订单模型
     * @param $id
     * @param array $select
     * @return OrderModel|mixed
     * @throws ResourceNotFoundException
     */
    public static function getOrderById($id, $select = [])
    {
        $orderModel = new OrderModel();
        if (!empty($select)) {
            $order = $orderModel->select($select)->find($id);
        } else {
            $order = $orderModel->find($id);
        }
        if (!$order) {
            Logger::fatal('orderMdl|order_not_exists|orderId:' . $id);
            throw new ResourceNotFoundException('订单不存在');
        }
        return $order;
    }

    /**
     * 获取两个订单之间的距离
     * @param $lng1
     * @param $lat1
     * @param $lng2
     * @param $lat2
     * @return float|int
     */
    public static function getDistance($lng1, $lat1, $lng2, $lat2)
    {
        $radLat1 = deg2rad($lat1); //deg2rad()函数将角度转换为弧度
        $radLat2 = deg2rad($lat2);
        $radLng1 = deg2rad($lng1);
        $radLng2 = deg2rad($lng2);
        $a = $radLat1 - $radLat2;
        $b = $radLng1 - $radLng2;
        $s = 2 * asin(sqrt(pow(sin($a / 2), 2) + cos($radLat1) * cos($radLat2) * pow(sin($b / 2), 2))) * 6378.137;
        return round($s,2) . 'km';
    }

    /**
     * 获取分页偏移量
     * @param $page
     * @param int $pageSize
     * @return array
     */
    private static function calculateLimitParam($page,$pageSize = 10){
        if (empty($page)){
            $offset = 0;
        } else{
            $offset = ($page - 1) * $pageSize;
        }
        return [
            'offset' => $offset,
            'size' => $pageSize
        ];
    }

    /**
     * 订单分页结果URL
     * @param $resCount
     * @param $currentPage
     * @param $baseUrl
     * @param int $pageSize
     * @return array
     */
    private static function calculatePage($resCount, $currentPage, $baseUrl, $pageSize = 10)
    {
        if ($resCount <= 0 || $pageSize <= 0 || $currentPage < 1) {
            return [];
        }
        $totalPage = intval(ceil($resCount / $pageSize));
        if ($totalPage <= 0) {
            $totalPage = 1;
        }
        if ($_SERVER['QUERY_STRING'] == ''){
            $firstPageUrl = $baseUrl . '?page=1';
            $lastPageUrl = $baseUrl . '?page=' . $totalPage;
            if ($currentPage == $totalPage){
                $nextPageUrl = null;
            } else{
                $nextPageUrl = $baseUrl . '?page='.($currentPage + 1);
            }
            if ($currentPage == 1){
                $prevPageUrl = null;
            } else{
                $prevPageUrl = $baseUrl . '?page=' . ($currentPage - 1);
            }
        } else{
            $firstPageUrl = $baseUrl . '&page=1';
            $lastPageUrl = $baseUrl . '&page=' . $totalPage;
            if ($currentPage == $totalPage){
                $nextPageUrl = null;
            } else{
                $nextPageUrl = $baseUrl . '&page='.($currentPage + 1);
            }
            if ($currentPage == 1){
                $prevPageUrl = null;
            } else{
                $prevPageUrl = $baseUrl . '&page=' . ($currentPage - 1);
            }
        }
        return [
            'first_page_url' => $firstPageUrl,
            'last_page_url' => $lastPageUrl,
            'current_page' => $currentPage,
            'next_page_url' => $nextPageUrl,
            'prev_page_url' => $prevPageUrl,
            'data_count' => $resCount,
            'total_page' => $totalPage
        ];
    }

    /**
     * 打包数据和分页结果一起返回给客户端
     * @param $midData
     * @param $curPage
     * @param $baseUrl
     * @param $offset
     * @param $limit
     * @return array
     */
    public static function packLimitData($midData,$curPage,$pageSize,$baseUrl){
        $limitParams = self::calculateLimitParam($curPage);
        $count = $midData->count();
        if ($count == 0){
            return [];
        }
        $offset = $limitParams['offset'];
        $limit = $limitParams['size'];
        $datas = $midData->offset($offset)->limit($limit)->get()->toArray();
        $limitRes = self::calculatePage($count,$curPage,$baseUrl,$pageSize);
        foreach ($datas as &$v){
            $v['content'] = str_limit($v['content'],100,'...');
            if (empty($v['avatar'])){
                if (!empty($v['sender_id'])){
                    $sender = UserModel::find($v['sender_id']);
                    $v['sender_avatar'] = $sender->avatar;
                }
                if (!empty($v['receiver_id'])){
                    $receiver = UserModel::find($v['receiver_id']);
                    $v['receiver_avatar'] = $receiver->avatar;
                }
                unset($v['receiver_id']);
                unset($v['sender_id']);
            }
        }
        return array_merge(['data' => $datas],$limitRes);
    }
}