<?php

/**
 * Discuz & Tencent Cloud
 * This is NOT a freeware, use is subject to license terms
 */

namespace App\Listeners\User;

use App\Events\Users\Registered;
use App\Models\Group;
use App\Models\Invite;
use App\Repositories\InviteRepository;
use Carbon\Carbon;
use Discuz\Contracts\Setting\SettingsRepository;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Arr;

class InviteBind
{
    protected $InviteRepository;

    protected $settings;

    public function __construct(InviteRepository $InviteRepository, SettingsRepository $settings)
    {
        $this->InviteRepository = $InviteRepository;
        $this->settings = $settings;
    }

    public function handle(Registered $event)
    {
        $code = Arr::get($event->data, 'code', '');

        if ($code) {
            $len = mb_strlen($code, 'utf-8');

            if ($len == 32) {
                //邀请码 32位长度为管理员邀请
                $invite = $this->InviteRepository->verifyCode($code);

                if ($invite) {
                    $invite->to_user_id = $event->user->id;
                    $invite->status = 2;
                    $invite->save();
                    //同步用户组
                    $defaultGroup = Group::find($invite->group_id);
                    $event->user->groups()->sync($defaultGroup->id);

                    //修改付费状态
                    if ($this->settings->get('site_mode') == 'pay') {
                        $event->user->expired_at = Carbon::now()->addDays($this->settings->get('site_expire'));
                        $event->user->save();
                    }
                }
            } else {
                $encrypter = app('encrypter');

                try {
                    $user_id = $encrypter->decryptString($code);
                } catch (DecryptException $e) {
//                    throw new DecryptException();
                    //邀请码解密失败后普通注册
                    return;
                }
                //生成记录
                Invite::insert([
                    'group_id' => 0,
                    'code' => $code,
                    'user_id' => $user_id,
                    'to_user_id' => $event->user->id,
                    'created_at' => Carbon::now()->toDate(),
                    'updated_at' => Carbon::now()->toDate(),
                ]);
            }
        }
    }
}
