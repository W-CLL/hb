
        var url = document.referrer;
        var html;
        html = "<link rel=\"stylesheet\" href=\"http://localhost:8080/static/bottommenu/5f867302ebb17/images/style.css\">"
            +" <footer style=\"height:50px;left:0;\">"
            +" <ul>"
        
            +"<li id=\"tz\" style=\"border-left: none;\">"
            +"<a href=\" http://localhost:8080/static/bottommenu/5f867302ebb17/jsq/?url="+url+"\" target=\"view_window\">"
            +"<div style=\"background: url(http://localhost:8080/static/bottommenu/5f867302ebb17/images/0f_1.gif) center -6px no-repeat;height: 35px;\"></div>"
            +"<div>投资计算</div>"
            +"</a>"
            +"</li>"
        
            +"<li id=\"xz\">"
            +"<a href=\"http://localhost:8080/static/bottommenu/5f867302ebb17/down/?url="+url+"\" target=\"view_window\">"
            +"<div style=\"background: url(http://localhost:8080/static/bottommenu/5f867302ebb17/images/0f_2.gif) center -6px no-repeat;height: 35px;\">"
            +"</div>"
            +"<div>加盟资料</div>"
            +"</a>"
            +"</li>"
        
            +"<li id=\"zx\">"
            +"<a href=\"\" target=\"view_window\">"
            +"<div style=\"background: url(http://localhost:8080/static/bottommenu/5f867302ebb17/images/0f_3.png) center -6px no-repeat;height: 25px;padding-top: 10px;\">"
            +"<div class=\"line\">3</div>"
            +"</div>"
            +"<div>在线咨询</div>"
            +"</a>"
            +"</li>"
        
            +"<li id=\"ly\">"
            +"<a href=\" http://localhost:8080/static/bottommenu/5f867302ebb17/jsq/?url="+url+"\" target=\"view_window\">"
            +"<div style=\"background: url(http://localhost:8080/static/bottommenu/5f867302ebb17/images/0f_4.gif) center -6px no-repeat;height: 35px;\"></div>"
            +"<div id=\"keyword\">留言</div>"
            +"</a>"
            +"</li>"
        
            +"<li id=\"ph\">"
            +"<a href=\"tel:\" target=\"view_window\">"
            +"<div style=\"background: url(http://localhost:8080/static/bottommenu/5f867302ebb17/images/0f_5.png) center -6px no-repeat;height: 35px;\"></div>"
            +"<div>电话咨询</div>"
            +"</a>"
            +"</li>"
        +"</ul></footer>";
        document.write(html);
        document.write("<a class='a1'></a>");
        var a1 = document.getElementsByClassName('a1')[0];
        a1.setAttribute("href", 'http://localhost:8080/static/bottommenu/5f867302ebb17/jsq/?url=' + url);
        