export default (to, from, next) => {
  document.title = `${ to.meta.title || to.name } | ${window.appSettings.appName}`.replace(/^./, match => match.toUpperCase())
}
