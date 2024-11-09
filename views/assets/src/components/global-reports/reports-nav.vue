<script>

</script>

<template>
  <div>
    <nav class="border navbar navbar-expand-lg navbar-light bg-light mb-3">
      <div class="container-fluid">
        <a class="navbar-brand" href="#">Reports</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
          <ul class="navbar-nav me-auto mb-2 mb-lg-0">
            <li class="nav-item">
              <router-link class="nav-link" active-class="active" to="/db-reports/home">Status</router-link>
            </li>
            <li class="nav-item">
              <router-link class="nav-link" active-class="active" to="/db-reports/reports">Reports</router-link>
            </li>
            <li class="nav-item">
              <router-link class="nav-link" active-class="active" to="/db-reports/parts">Parts</router-link>
            </li>
            <li class="nav-item">
              <router-link class="nav-link" active-class="active" to="/db-reports/wip">WIP Schedule</router-link>
            </li>
          </ul>
        </div>
      </div>
    </nav>
    <router-view />
  </div>
</template>

<style scoped lang="less">

</style>
