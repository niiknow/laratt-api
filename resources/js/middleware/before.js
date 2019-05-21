import expired from './before/expired'
import keepid from './before/keepid'
import allows from './before/allows'

export default (to, from, next) => {
  expired(to, from, next)
  keepid(to, from, next)
  allows(to, from, next)
}
