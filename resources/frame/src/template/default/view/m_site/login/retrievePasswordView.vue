<template>
    <div class="retrieve-password-box">
      <retrievePWDHeader></retrievePWDHeader>
      <main class="retrieve-password-main">

        <div class="login-module-title-box">
          <h2 class="login-module-title" v-if="type === 'reset_pwd'">忘记密码</h2>
          <h2 class="login-module-title" v-else>忘记钱包密码</h2>
        </div>

        <div class="login-module-form">
          <van-cell-group>
            <van-field
              label="手机号"
              :clearable="type !== 'reset_pay_pwd'"
              v-model="phoneNum"
              :readonly="type === 'reset_pay_pwd'"
              placeholder="请输入您的手机号"
              maxlength="13"
            />

            <van-field
              v-model="verifyNum"
              center
              clearable
              label="验证码"
              placeholder="请输入验证码"
              type="number"
            >
              <van-button slot="button" size="small" type="default" @click="forgetSendSmsCode" :class="{'grayBg':isGray}">{{ btnContent }}</van-button>
            </van-field>

            <van-field
              v-if="type === 'reset_pwd'"
              label="新密码"
              clearable
              v-model="newpwd"
              type="password"
              placeholder="请输入新密码"
            />


            <van-field
              v-if="type === 'reset_pay_pwd'"
              label="新密码"
              clearable
              v-model="payPassword"
              type="password"
              maxlength="6"
              placeholder="请输入新密码"
            />

            <van-field
              v-if="type === 'reset_pay_pwd'"
              label="确认密码"
              clearable
              v-model="payPasswordConfirmation"
              type="password"
              maxlength="6"
              placeholder="请输入新密码"
            />

          </van-cell-group>
        </div>


        <div class="retrieve-password-btn">
          <van-button type="primary" :loading="btnLoading" @click="submissionPassword">提交</van-button>
        </div>

      </main>
    </div>
</template>

<script>
import '../../../defaultLess/m_site/modules/loginSignUpModule.less'
import '../../../defaultLess/m_site/common/common.less'
import retrievePasswordCon from '../../../controllers/m_site/login/retrievePasswordCon';
export default {
    name: "retrieve-password-view",
  ...retrievePasswordCon
}
</script>
