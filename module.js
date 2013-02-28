M.local_mmrdebugger = {};
M.local_mmrdebugger.Y = null;
M.local_mmrdebugger.cfg = null;
M.local_mmrdebugger.streamWindowInstance = null;
M.local_mmrdebugger.streaming = false;
M.local_mmrdebugger.streamWindowSet = false;

M.local_mmrdebugger.init = function(Y, cfg) {
    M.local_mmrdebugger.Y = Y;
    M.local_mmrdebugger.cfg = cfg;
    
    Y.one("#screenshotbutton").on('click', function() {
        Y.one("#uactions").setAttribute('src', 'user.php?id='+cfg.userid+'&type=screenshot');
    });
    
    Y.one("#streampagebutton").on('click', function() {
        if (M.local_mmrdebugger.streaming) {
            Y.one("#uactions").setAttribute('src', 'user.php?id='+cfg.userid+'&type=stopstreampage');
            M.local_mmrdebugger.streamWindowInstance.close();
            M.local_mmrdebugger.streamWindowInstance = null;
            M.local_mmrdebugger.streaming = false;
        } else {
            Y.one("#uactions").setAttribute('src', 'user.php?id='+cfg.userid+'&type=streampage');
            M.local_mmrdebugger.streaming = true;
        }
    });
    
    Y.one("#userf").on("submit", function() {
       setTimeout(function(){ Y.one("#command").set("value", "") }, 1000); 
    });
};

M.local_mmrdebugger.streamWindow = function (t, w, h) {
    if (M.local_mmrdebugger.streaming) {
        if (! M.local_mmrdebugger.streamWindowInstance) {
            var newW = w + 200;
            var newH = h + 100;
            M.local_mmrdebugger.streamWindowInstance = open('stream.php?id=' + M.local_mmrdebugger.cfg.userid, 'streamwindow', 'width='+newW+', height='+newH);
        }
        var ifrm = M.local_mmrdebugger.streamWindowInstance.document.getElementById('streamiframe');
        
        if (ifrm) {
            if (! M.local_mmrdebugger.streamWindowSet) {
                Y.one(ifrm).set('width', w);
                Y.one(ifrm).set('height', h);
                Y.one(M.local_mmrdebugger.streamWindowInstance.document.getElementById('startinspection')).on('click', function() {
                    M.local_mmrdebugger.streaming = false;
                    setTimeout(M.local_mmrdebugger.inspectorSync, 5000);
                });
                M.local_mmrdebugger.streamWindowSet = true;
            }
    
            ifrm = (ifrm.contentWindow) ? ifrm.contentWindow : (ifrm.contentDocument.document) ? ifrm.contentDocument.document : ifrm.contentDocument;
            ifrm.document.open();
            ifrm.document.write(decodeURIComponent(t));
            ifrm.document.close();
        }
    }
};

M.local_mmrdebugger.inspectorSync = function() {
    var ifrm = M.local_mmrdebugger.streamWindowInstance.document.getElementById('streamiframe');
        
    if (ifrm) {
        ifrm = (ifrm.contentWindow) ? ifrm.contentWindow : (ifrm.contentDocument.document) ? ifrm.contentDocument.document : ifrm.contentDocument;
        var html = Y.one(ifrm.document).one("body").getHTML();
        var url = "user.php";
        var cfg = {
            method: 'POST',
            data: 'id='+M.local_mmrdebugger.cfg.userid+'&type=inspector&command='+encodeURIComponent(html)
        }
        M.local_mmrdebugger.Y.io(url, cfg);
        setTimeout(M.local_mmrdebugger.inspectorSync, 5000);
    }
}