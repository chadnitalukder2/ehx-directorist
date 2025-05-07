<template>
  <div id="ehx-directorist_app" class="ehxd-admin-page">
    <div class="ehxd-main-menu">
      <div class="ehxd-menu-logo">
        <h3>EHX Directorist</h3>
      </div>
      <div class="menu-item">
        <router-link class="ehxd-menu-item" to="/">
          Dashboard
        </router-link>
        <!-- <router-link class="ehxd-menu-item" to="/menus">
          Menus
        </router-link> -->
        <!-- <router-link class="ehxd-menu-item" to="/shipments">
          Shipments
        </router-link> -->
        <router-link class="ehxd-menu-item" to="/settings">
          Settings
        </router-link>
      </div>
    </div>
    <router-view></router-view>
  </div>

  <!-- =========================================== -->

</template>

<script>
export default {
  name: 'App',
}
</script>

<style lang="scss">

</style>
