onScroll();
function onScroll()
{
  // console.log(inViewport($('#ad')));
  var sH = $(window).scrollTop();// + $('#nav').outerHeight();
  var cH = document.body.clientHeight - $('#nav').outerHeight();
  var adH = $('#ad').outerHeight();
  // console.log((cH - adH)/2 + sH);
  document.getElementById("ad").style.top = ((cH - adH)/2 + sH) + "px";
  // console.log(cH + " " + adH + " " + sH)
}

function inViewport($el) {
  // console.log();
  var htemp = $(window).height() - $('#nav').outerHeight();
  // console.log(htemp)
  var elH = $el.outerHeight(),
      H   = htemp,
      r   = $el[0].getBoundingClientRect(), t=r.top, b=r.bottom;
  return Math.max(0, t>0? Math.min(elH, H-t) : Math.min(b, H));
}
