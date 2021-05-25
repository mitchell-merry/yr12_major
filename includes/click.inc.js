
function bodyClick(e)
{
  var e = e || window.event;
  pending(e);
}

function pending(e)
{
    var rows = document.getElementsByClassName("pending-menu");
    for(var i=0; i < rows.length; i++)
    {
      var r = rows[i]
      var num = r.id.split("-")[1];
      var butt = document.getElementById("button-" + num);
      if(!r.classList.contains("hidden") && !r.contains(e.target) && !butt.contains(e.target))
      {
        r.classList.add("hidden");
      }
    }
}
