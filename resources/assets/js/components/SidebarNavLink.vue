<template>
  <router-link :to="url" class="nav-link" :class="addClass">
    <i :class="icon"></i> {{name}}
    <b-badge :variant="badge.variant">{{badge.text}}</b-badge>
  </router-link>
</template>

<script>
export default {
  name: 'sidebar-nav-link',
  props: {
    name: {
      type: String,
      default: ''
    },
    url: {
      type: String,
      default: ''
    },
    icon: {
      type: String,
      default: ''
    },
    badge: {
      default: ''
    },
    addClass: {
        type: String,
        default: ''
    }
  }
}
</script>

<style lang="css">
  .nav-link {
    cursor:pointer;
  }
  .child{
    margin-left: 20px;
    font-size: 0.8rem;
  }
</style>
