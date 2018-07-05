<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/4/004
 * Time: 23:10
 */

namespace App\Service;

use App\Helper\ConstHelper;
use Yansongda\Pay\Pay;

class AliService{

    private static $config = [
            'app_id' => '2017090608582098',
            'notify_url' => ConstHelper::HOST . 'pay/aliNotify',
            //'return_url' => 'http://yansongda.cn/return.php',
            'ali_public_key' => 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA28Z6LQILxsm7xx9gxb5l7UvzwsSpIn7rzQwTKnnH8VNIObxPbEmzGL32vhfcrrWY+7qdaLlubGWwsCt3AoIC+ZfznDrsy0Sswsi9qXv/9ShQHhS0aUKem8eESyl3bpeeyZUP8L35vuH6Pqsvp/5aaA0wLQVKV4zv7hgi6p1D7PtB48o9F4dI4hUfDa/fFdRjVjaP5eUnoB0Vw0LnNj/8HAXtPTUD9TxkWXO2sLRhJQXhvqHrIR4tW/5zMkIL8/qKlovjpR8NRip5KD4tq9GWVa6oVlEbVCSvuNXKJJUhYT3+mfRZKTJbQFjDAYHxWh4k2kXAjSFDXNX4dSSmdIIzKQIDAQAB',
            'private_key' => 'MIIEvgIBADANBgkqhkiG9w0BAQEFAASCBKgwggSkAgEAAoIBAQDbxnotAgvGybvHH2DFvmXtS/PCxKkifuvNDBMqecfxU0g5vE9sSbMYvfa+F9yutZj7up1ouW5sZbCwK3cCggL5l/OcOuzLRKzCyL2pe//1KFAeFLRpQp6bx4RLKXdul57JlQ/wvfm+4fo+qy+n/lpoDTAtBUpXjO/uGCLqnUPs+0Hjyj0Xh0jiFR8Nr98V1GNWNo/l5SegHRXDQuc2P/wcBe09NQP1PGRZc7awtGElBeG+oeshHi1b/nMyQgvz+oqWi+OlHw1GKnkoPi2r0ZZVrqhWURtUJK+41coklSFhPf6Z9FkpMltAWMMBgfFaHiTaRcCNIUNc1fh1JKZ0gjMpAgMBAAECggEALZ+tW0ySb0kPv02HRGW7OSDMUMGMrwmUH/QCwo2XcUStuLab8kn2cQt2fo3rlSVDxfY+mS/teXk+zcOoKBAfV/swal1dLPFrv9/2Z4nDX/xnbWc08KkQzhwEHapVDdNR9l0IexylDPhSf9H/yasmz3T1bFMt1LEAWfgOv4+4OyinC3QJed0S6L0K6IPhUKGS58BqXLc3AbJJhxx7AOMu6V1H6lyueO1a/rLdmIy/3EmyD4MeEXhTEYZ9Pou5ldcg1+DrDra1TPU0NStn7kjG9x2simG9+w0CyD56EInMehd8xSBUHF5CySV754o1AJvRmIqJd8o09Aug8WH8gowR0QKBgQD/bFS/EUQMuU0zWVN3UkdA7XJDhXYsM6u9NgptDS0vSe0lGgSdkXpdDyxYyYNBC4cwwP5Un2nV+e1SvDnoi7/SooDYRWqT1mxyFmiCv66MO5oXdZgRb6Y+r8HcdTAoKHtew5wq0qCZ45X9l5AspGIDwP//HSERTEz0IdthMkswfQKBgQDcRYl1Qf66XagSIumbvprJAWHd054yvwEoNEsMCejclCYeFGNSIa1eirDPPx2MY3PYsJ6IvZvx45vY4cuL3cg5tmeHfsG9Y7pRUB/4+sNLvHl3kYSz7GDTzI8KLFbcW+3eFwPqb0mSaECw4mzRnx7k0oT/geH2TFxwyLjWFH+ZHQKBgE8M/RGBS3lQpDb/L1jfixPqKRICrTcy6rUNk556lIBtNcrkyYbmrmM8vfHgtBGeesG5CT2xdLT3u95+SMHS9pQ/HPdSTJDirP+GNeQ5ZNEb5S3bhCvpTR9bj/kl/7h+BuimS5/pPjFCgXpRvRpD6d3VjqUrI7/RyINKMzZatXCVAoGBAIgmAvyTOht/YC81nSdC/PFZBWDTOGktXk23ZNugFGqit5zIBUvyvPI+z2KKJH1ty2EYaiUi7Yzpnp7Dkch14RirAfriAmZJihRQbjK67JOXf4zEST3c0UyYl6E2Hso9mB06JJV3DAaOoc2zZod6zuGorcQwR7axJEIiDv1j1iqRAoGBALDv/sWEWHr4435jR6FqPgEVzE/jarvzBYyR7O6Fq6KgxgHXL5VejELYepg1ebUQ6VXDVF9pYH9TqunGFdofwdwAQxGUgNlCy3km3gDs72haqUuyOztbgwF3n1NtcPDuJ2V48VrTPkq2Tsjf2uzT7HUaPq3WGsSs+7+6pHVGrtxP',
            'log' => [ // optional
                'file' => '.storage/logs/alipay.log',
                'level' => 'debug'
            ],
    ];


    /**
     * 返回支付宝操作实例
     * @return \Yansongda\Pay\Gateways\Alipay
     */
    public static function getAliPayApp(){
        return Pay::alipay(self::$config);
    }
}