
// guard against clearing from project id
export default (to, from, next) => {
  if(!to.query.sid && from.query.sid) {
    to.query.sid = from.query.sid
    next({ path: to.path, query: to.query })
  }
}
