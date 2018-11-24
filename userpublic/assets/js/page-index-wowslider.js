function ws_cube(k, g, a) {
    var c = jQuery,
    e = c("ul", a),
    b = k.perspective || 2000;
    fullContCSS = {
        position: "absolute",
        backgroundSize: "cover",
        left: 0,
        top: 0,
        width: "100%",
        height: "100%",
        backfaceVisibility: "hidden"
    };
    var j = {
        domPrefixes: " Webkit Moz ms O Khtml".split(" "),
        testDom: function(m) {
            var l = this.domPrefixes.length;
            while (l--) {
                if (typeof document.body.style[this.domPrefixes[l] + m] !== "undefined") {
                    return true
                }
            }
            return false
        },
        cssTransitions: function() {
            return this.testDom("Transition")
        },
        cssTransforms3d: function() {
            var m = (typeof document.body.style.perspectiveProperty !== "undefined") || this.testDom("Perspective");
            if (m && /AppleWebKit/.test(navigator.userAgent)) {
                var o = document.createElement("div"),
                l = document.createElement("style"),
                n = "Test3d" + Math.round(Math.random() * 99999);
                l.textContent = "@media (-webkit-transform-3d){#" + n + "{height:3px}}";
                document.getElementsByTagName("head")[0].appendChild(l);
                o.id = n;
                document.body.appendChild(o);
                m = o.offsetHeight === 3;
                l.parentNode.removeChild(l);
                o.parentNode.removeChild(o)
            }
            return m
        },
        webkit: function() {
            return /AppleWebKit/.test(navigator.userAgent) && !/Chrome/.test(navigator.userAgent)
        }
    };
    var d = (j.cssTransitions() && j.cssTransforms3d()),
    h = j.webkit();
    if (!d && k.fallback) {
        return new k.fallback(k, g, a)
    }
    function i(l, m, o, n) {
        return "inset " + ( - n * l * 1.2 / 90) + "px " + (o * m * 1.2 / 90) + "px " + (l + m) / 20 + "px rgba(" + ((o < n) ? "0,0,0,.6": (o > n) ? "255,255,255,0.8": "0,0,0,.0") + ")"
    }
    var f;
    this.go = function(s, p) {
        function m(K, z, A, y, x, I, J, G, F) {
            K.parent().css("perspective", b);
            var E = K.width(),
            C = K.height();
            var H = c(K.children().get(1));
            H.css({
                transform: "rotateY(0deg) rotateX(0deg)",
                boxShadow: i(E, C, 0, 0)
            });
            var B = c(K.children().get(0));
            B.css({
                opacity: 1,
                transform: "rotateY(" + I + "deg) rotateX(" + x + "deg)",
                boxShadow: i(E, C, x, I)
            });
            if (h) {
                K.css({
                    transform: "translateZ(-" + z + "px)"
                })
            }
            var D = setTimeout(function() {
                var w = "all " + k.duration + "ms cubic-bezier(0.645, 0.045, 0.355, 1.000)";
                H.css({
                    transition: w,
                    boxShadow: i(E, C, J, G),
                    transform: "rotateX(" + J + "deg) rotateY(" + G + "deg)"
                });
                B.css({
                    transition: w,
                    boxShadow: i(E, C, 0, 0),
                    transform: "rotateY(0deg) rotateX(0deg)"
                });
                D = setTimeout(F, k.duration)
            },
            20);
            return {
                stop: function() {
                    clearTimeout(D);
                    F()
                }
            }
        }
        if (d) {
            if (f) {
                f.stop()
            }
            var u = a.width(),
            q = a.height();
            var v = c('<div class="ws_effect">').css(fullContCSS).css({
                transformStyle: "preserve-3d",
                perspective: h ? "none": b,
                zIndex: 8
            }).appendTo(a.parent());
            var o = {
                left: [u / 2, u / 2, 0, 0, 90, 0, -90],
                right: [u / 2, -u / 2, 0, 0, -90, 0, 90],
                down: [q / 2, 0, -q / 2, 90, 0, -90, 0],
                up: [q / 2, 0, q / 2, -90, 0, 90, 0]
            } [k.direction || ["left", "right", "down", "up"][Math.floor(Math.random() * 4)]];
            c("<div>").css(fullContCSS).appendTo(v).css({
                backgroundImage: "url(" + g.get(s).src + ")",
                transformOrigin: "50% 50% -" + o[0] + "px"
            });
            c("<div>").css(fullContCSS).appendTo(v).css({
                backgroundImage: "url(" + g.get(p).src + ")",
                transformOrigin: "50% 50% -" + o[0] + "px"
            });
            e.hide();
            f = new m(v, o[0], o[1], o[2], o[3], o[4], o[5], o[6],
            function() {
                e.css({
                    left: -s + "00%"
                }).show();
                v.remove();
                f = 0
            })
        } else {
            var l = c("<div></div>").css({
                position: "absolute",
                display: "none",
                zIndex: 2,
                width: "100%",
                height: "100%"
            }).appendTo(a);
            l.stop(1, 1);
            var n = ( !! ((s - p + 1) % g.length) ^ k.revers ? "left": "right");
            var t = c(g[p]).clone().css({
                position: "absolute",
                left: "0%",
                right: "auto",
                top: 0,
                width: "100%",
                height: "100%"
            }).appendTo(l).css(n, 0);
            var r = c(g[s]).clone().css({
                position: "absolute",
                left: "100%",
                right: "auto",
                top: 0,
                width: "0%",
                height: "100%"
            }).appendTo(l).show();
            l.css({
                left: "auto",
                right: "auto",
                top: 0
            }).css(n, 0).show();
            e.hide();
            r.animate({
                width: "100%",
                left: 0
            },
            k.duration, "easeInOutExpo",
            function() {
                c(this).remove()
            });
            t.animate({
                width: 0
            },
            k.duration, "easeInOutExpo",
            function() {
                e.css({
                    left: -s + "00%"
                }).show();
                l.remove()
            })
        }
        return s
    }
};
jQuery("#wowslider-container").wowSlider({
    effect: "cube",
    prev: "",
    next: "",
    duration: 20 * 100,
    delay: 20 * 100,
    width: 695,
    height: 345,
    autoPlay: true,
    playPause: true,
    stopOnHover: false,
    loop: false,
    bullets: true,
    caption: true,
    captionEffect: "slide",
    controls: true,
    onBeforeStep: 0,
    images: 0
});