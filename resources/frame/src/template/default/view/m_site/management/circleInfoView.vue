<!--移动端付费站点模板-->

<template>
    <div class="circleCon">
	    <comHeader title="站点信息"></comHeader>
		<van-pull-refresh v-model="isLoading" @refresh="onRefresh">
	    <div class="content">
        <div v-if="siteInfo">
          <div class="circlePL">
          	<div class="infoItem">
            	<span class="infoItemLeft">站点名称</span>
            	<span class="infoItemRight">{{siteInfo._data.set_site.site_name}}</span>
            </div>
          </div>
          <div class="circlePL">
          	<div class="circleLoBox">
            	<span class="circleIcon">站点图标</span>
              <img v-if="siteInfo._data.set_site.site_logo" :src="siteInfo._data.set_site.site_logo" class="circleLogo">
              <img v-else="" :src="appConfig.staticBaseUrl+'/images/logo.png'" class="circleLogo">
            </div>
          </div>
          <div class="circleInfo padB0 lastBorNone">
          	<h1 class="cirInfoTit">站点简介</h1>
          	<p class="cirInfoWord">{{siteInfo._data.set_site.site_introduction}}</p>
          	<div class="infoItem">
            	<span class="infoItemLeft">创建时间</span>
            	<span class="infoItemRight">{{siteInfo._data.set_site.site_install}}</span>
            </div>
            <div class="infoItem" v-if="siteInfo._data.set_site.site_price || siteInfo._data.set_site.site_expire">
            	<span class="infoItemLeft">加入方式</span>
            	<span class="infoItemRight">付费{{siteInfo._data.set_site.site_price}}元，{{siteInfo._data.set_site.site_expire === '0' || siteInfo._data.set_site.site_expire === ''?'永久加入':'有效期自加入起'+ siteInfo._data.set_site.site_expire +'天'}}</span>
            </div>
            <div class="infoItem">
            	<span class="infoItemLeft">站长</span>
            	<span class="infoItemRight" v-if="siteInfo._data.set_site.site_author">{{username}}</span>
              <span class="infoItemRight" v-else="">无</span>
            </div>
            <div class="infoItem">
            	<div class="overHide">
            		<span class="infoItemLeft">站点成员</span>
            		<a v-if="moreMemberShow" class="infoItemRight lookMore" @click="moreCilrcleMembers">查看更多<span class="icon iconfont icon-right-arrow"></span></a>
            	</div>
            	<div class="circleMemberList">
                <img v-for="(item,index) in siteInfo.users" :key="item._data.avatarUrl" :src="item._data.avatarUrl" :alt="item._data.username" class="circleMember" v-if="item._data.avatarUrl !== '' && item._data.avatarUrl !== null">
                <img :src="appConfig.staticBaseUrl+'/images/noavatar.gif'" class="circleMember" v-else @click="membersJump(item._data.id)">
            	</div>
            </div>
          </div>
          <div class="gap"></div>
          <div class="circleInfo padT0">
          	<div class="infoItem">
            	<span class="infoItemLeft">我的角色</span>
            	<span class="infoItemRight" v-for="(role,ro) in roleList">{{role._data.name}}</span>
            </div>
            <div class="infoItem">
            	<span class="infoItemLeft">加入时间</span>
            	<span class="infoItemRight">{{$dayjs(joinedAt).format('YYYY-MM-DD')}}</span>
            </div>
            <div class="infoItem" v-if="expiredAt">
            	<span class="infoItemLeft">有效期至</span>
            	<span class="infoItemRight">{{$dayjs(expiredAt).format('YYYY-MM-DD')}}</span>
            </div>
          </div>
          <div class="powerListBox" v-if="limitList">
          	<div class="powerTit">作为{{limitList._data.name}}，您将获得以下权限</div>
          	<div class="powerList">
          		<div class="powerClassify">权限列表</div>
              <div class="" v-for="(limit) in limitList.permission" :key="limit">
                <p class="powerChi" v-if="limit._data.permission && limit._data.permission == 'viewThreads'">查看主题列表<i class="iconfont icon-selected"></i></p>
                <p class="powerChi" v-if="limit._data.permission && limit._data.permission == 'thread.viewPosts'">查看主题<i class="iconfont icon-selected"></i></p>
                <p class="powerChi" v-if="limit._data.permission && limit._data.permission == 'createThread'">发表主题<i class="iconfont icon-selected"></i></p>
                <p class="powerChi" v-if="limit._data.permission && limit._data.permission == 'thread.reply'">回复主题<i class="iconfont icon-selected"></i></p>
                <p class="powerChi" v-if="limit._data.permission && limit._data.permission == 'attachment.create.0'">上传附件<i class="iconfont icon-selected"></i></p>
                <p class="powerChi" v-if="limit._data.permission && limit._data.permission == 'attachment.create.1'">上传图片<i class="iconfont icon-selected"></i></p>
                <p class="powerChi" v-if="limit._data.permission && limit._data.permission == 'attachment.view.0'">查看附件<i class="iconfont icon-selected"></i></p>
                <p class="powerChi" v-if="limit._data.permission && limit._data.permission == 'attachment.view.1'">查看图片<i class="iconfont icon-selected"></i></p>
                <p class="powerChi" v-if="limit._data.permission && limit._data.permission == 'viewUserList'">站点会员列表<i class="iconfont icon-selected"></i></p>
                <p class="powerChi" v-if="limit._data.permission && limit._data.permission == 'attachment.delete'">删除附件<i class="iconfont icon-selected"></i></p>
                <p class="powerChi" v-if="limit._data.permission && limit._data.permission == 'cash.create'">申请提现<i class="iconfont icon-selected"></i></p>
                <p class="powerChi" v-if="limit._data.permission && limit._data.permission == 'order.create'">创建订单<i class="iconfont icon-selected"></i></p>
                <p class="powerChi" v-if="limit._data.permission && limit._data.permission == 'thread.hide'">删除主题<i class="iconfont icon-selected"></i></p>
                <p class="powerChi" v-if="limit._data.permission && limit._data.permission == 'thread.hidePosts'">删除回复<i class="iconfont icon-selected"></i></p>
                <p class="powerChi" v-if="limit._data.permission && limit._data.permission == 'thread.favorite'">帖子收藏<i class="iconfont icon-selected"></i></p>
                <p class="powerChi" v-if="limit._data.permission && limit._data.permission == 'thread.likePosts'">帖子点赞<i class="iconfont icon-selected"></i></p>
                <p class="powerChi" v-if="limit._data.permission && limit._data.permission == 'user.view'">查看某个用户信息权限<i class="iconfont icon-selected"></i></p>
                <p class="powerChi" v-if="limit._data.permission && limit._data.permission == 'viewSiteInfo'">站点信息<i class="iconfont icon-selected"></i></p>
                <p class="powerChi" v-if="limit._data.permission && limit._data.permission == 'user.edit'">编辑用户状态<i class="iconfont icon-selected"></i></p>
                <p class="powerChi" v-if="limit._data.permission && limit._data.permission == 'group.edit'">编辑用户组<i class="iconfont icon-selected"></i></p>
                <p class="powerChi" v-if="limit._data.permission && limit._data.permission == 'createInvite'">管理-邀请加入<i class="iconfont icon-selected"></i></p>
                <p class="powerChi" v-if="limit._data.permission && limit._data.permission == 'thread.batchEdit'">批量管理主题<i class="iconfont icon-selected"></i></p>
                <p class="powerChi" v-if="limit._data.permission && limit._data.permission == 'thread.editPosts'">编辑<i class="iconfont icon-selected"></i></p>
                <p class="powerChi" v-if="limit._data.permission && limit._data.permission == 'thread.essence'">加精<i class="iconfont icon-selected"></i></p>
                <p class="powerChi" v-if="limit._data.permission && limit._data.permission == 'thread.sticky'">置顶<i class="iconfont icon-selected"></i></p>
              </div>
          	</div>
          </div>
        </div>
	    </div>
		</van-pull-refresh>
    </div>
</template>

<script>
import comHeader from '../../../view/m_site/common/loginSignUpHeader/loginSignUpHeader';
import mSiteCircleInfoCon from '../../../controllers/m_site/management/circleInfoCon';
import  '../../../defaultLess/m_site/common/common.less';
import  '../../../defaultLess/m_site/modules/circle.less';
export default {
    name: "circleInfoView",
    components:{
      comHeader
    },
    ...mSiteCircleInfoCon

}



</script>
