<?php


namespace App\Commands\Users;


use App\Censor\Censor;
use App\Censor\CensorNotPassedException;
use App\Events\Users\Registered;
use App\Events\Users\Saving;
use App\Exceptions\TranslatorException;
use App\Models\User;
use App\Settings\SettingsRepository;
use App\Validators\UserValidator;
use Carbon\Carbon;
use Discuz\Foundation\EventsDispatchTrait;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class RegisterWechatQyUser
{

    use EventsDispatchTrait;

    /**
     * The user performing the action.
     *
     * @var User
     */
    public $actor;

    /**
     * The attributes of the new user.
     *
     * @var array
     */
    public $data;

    /**
     * @param User $actor The user performing the action.
     * @param array $data The attributes of the new user.
     */
    public function __construct(User $actor, array $data)
    {
        $this->actor = $actor;
        $this->data = $data;
    }

    /**
     * @param Dispatcher $events
     * @param Censor $censor
     * @param SettingsRepository $settings
     * @param UserValidator $validator
     * @return User
     * @throws ValidationException
     * @throws TranslatorException
     */
    public function handle(Dispatcher $events, Censor $censor, SettingsRepository $settings, UserValidator $validator)
    {
        $this->events = $events;

        $this->data['password'] = '';

        // 敏感词校验
        try {
            $censor->checkText(Arr::get($this->data, 'username'), 'username');
            $user = User::where('username', Arr::get($this->data, 'username'))->first();
            if ($user) {
                throw new CensorNotPassedException();
            }
        } catch (CensorNotPassedException $e) {
            $this->data['username'] = $this->getNewUsername();
        }

        // 审核模式，设置注册为审核状态
        if ($settings->get('register_validate')) {
            $this->data['register_reason'] = '企业微信注册';
            $this->data['status'] = 2;
        }

        // 付费模式，默认注册时即到期
        if ($settings->get('site_mode') == 'pay') {
            $this->data['expired_at'] = Carbon::now();
        }

        $user = User::register(Arr::only($this->data, ['username', 'password', 'register_ip', 'register_reason', 'status']));

        $this->events->dispatch(
            new Saving($user, $this->actor, $this->data)
        );

        $user->save();

        $user->raise(new Registered($user, $this->actor, $this->data));

        $this->dispatchEventsFor($user, $this->actor);

        return $user;
    }


    private function getNewUsername()
    {
        $username = trans('validation.attributes.username_prefix') . Str::random(6);
        $user = User::where('username', $username)->first();
        if ($user) {
            return $this->getNewUsername();
        }
        return $username;
    }

}
