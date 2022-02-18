<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User\UserModel;
use App\Models\User\UserOpLogModel;
use Carbon\Carbon;
use Log;
use DB;

class DailyCheck extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:daily_check_user_forbidden';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '每天检测禁言账户中是否有需要恢复的用户';

    private $userModel;
    private $userOpLogModel;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(
        UserModel $userModel,
        UserOpLogModel $userOpLogModel
    ) {
        parent::__construct();
        $this->userModel = $userModel;
        $this->userOpLogModel = $userOpLogModel;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('command daily_check_user_forbidden start');

        $operationInfo = [
            'operator_id' => 1,
            'operator_type' => 20,
            'operator_ip' => '127.0.0.1'
        ];

        $userList = $this->userModel->getAll(
            [
                'status' => config('display.user_status.forbidden.code'),
                'is_del' => 0,
                'forbidden_end_lte' => Carbon::today()->toDateTimeString()
            ], [
                'id' => 'desc'
            ], [
                'id', 'status', 'forbidden_end',
            ]
        );

        if ($userList) {
            $new = [];
            $origin = [];
    
            $status = config('display.user_status.available.code');
            foreach ($userList as $userInfo) {
                $userId = $userInfo['id'] ?? 0;
                $new[$userId] = [
                    'forbidden_end' => '',
                    'status' => $status
                ];

                $origin[$userId] = [
                    'forbidden_end' => $userInfo['forbidden_end'] ?? '',
                    'status' => $userInfo['status'] ?? 0
                ];
            }

            $userIds = array_column($userList, 'id');
            DB::transaction(function () use ($userIds, $status, $new, $origin, $operationInfo) {
                try {
                    $this->userModel
                        ->whereIn('id', $userIds)
                        ->update([
                            'status' => $status,
                            'forbidden_end' => ''
                        ]);
                    $this->userOpLogModel->saveUpdateOpLogDatas(
                        $new,
                        $origin,
                        $operationInfo,
                        '禁言时间结束',
                    );
                } catch (\Exception $e) {
                    Log::error(sprintf('修改禁言状态失败[UserIds][%s][Time][%s][Code][%s][Message][%s]', json_encode($userIds), Carbon::now()->toDateTimeString(), $e->getCode(), $e->getMessage()));
                }
            });

            Log::info(sprintf('修改禁言状态成功[UserIds][%s][Time][%s]', json_encode($userIds), Carbon::now()->toDateTimeString()));
            
        }

        $this->info('command daily_check_user_forbidden end');

    }
}
