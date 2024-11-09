<script>
export default {
  name: "cute-menu"
}
</script>

<template>
  <div class="gk-cute-menu">
    <div class="gk-cm-dialog-box">
      <slot/>
    </div>
  </div>
</template>

<style scoped lang="less">
.gk-cute-menu {
  position: relative;
  display: flex;
  justify-content: space-between;
  align-items: center;
  width: 40px;
  overflow: visible;
}
.gk-cm-dialog-box {
  position: absolute;
  top: 0;
  right: 0;
  width: 200px;
  padding: 10px;
  background-color: #fff;
  border: 1px solid #000;
  z-index: 1;
  overflow: hidden;
}
</style>
