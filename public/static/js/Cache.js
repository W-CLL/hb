/**
 *  本地缓存
 *  sessionStorage 创建一个本地存储的 name/value 对
 *  sessionStorage 用于临时保存同一窗口(或标签页)的数据，在关闭窗口或标签页之后将会删除这些数据。
 *  localStorage 属性,该数据对象没有过期时间,除非你手动去删除
 */
function Cache(type = 'session') {
    var cache = {
        type: type,
        setType: function () {
            if (this.type == 'session') {
                return window.sessionStorage;
            }
            if (this.type == 'local') {
                return window.localStorage;
            }
            console.log('类型错误！');
        },
        set: function ($key, $value) {
            this.setType().setItem($key, $value);
        },
        get: function ($key) {
            return this.setType().getItem($key);
        },
        remove: function ($key) {
            return this.setType().removeItem($key);
        },
        clear: function () {
            return this.setType().clear();
        }
    }
    return cache;
}
