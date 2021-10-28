function round(num) {
    var m = Number((Math.abs(num) * 100).toPrecision(15));
    return Math.round(m) / 100 * Math.sign(num);
}

function formattedDate(d = new Date) {
  let month = String(d.getMonth() + 1);
  let day = String(d.getDate());
  const year = String(d.getFullYear());

  if (month.length < 2) month = '0' + month;
  if (day.length < 2) day = '0' + day;

  return `${day}.${month}.${year}`;
}

$( document ).ready(function() {
    
    
    const date = new Date();
    let endDate = new Date();
    
    let val = $('#cpd');
    let dend = $('#aend');
    let s = $('#static_ad_credits');
    let d = $('#static_ad_days');
    let b = $('#b_data');
    let x = {};
    let credits, days;
    
    x.rate = (typeof b.data('rate') === 'undefined') ? 0 : b.data('rate');
    x.days = (typeof b.data('days') === 'undefined') ? 0 : parseInt(b.data('days'));
    x.credits = (typeof b.data('credits') === 'undefined') ? 0 : b.data('credits');
        
    s.val(0);
    d.val(1);
    
    if (x.days === 0 ) {
        endDate.setDate(date.getDate() + 1);
        dend.html(formattedDate(endDate));
    }
    
    s.keyup(function() {
       endDate = new Date();
      credits = parseInt($( this ).val()) + x.credits;
      days = parseInt(d.val()) + x.days;
      if(days < 1) {
        days = 1;
      } console.log((days));
      val.html(round(credits/days));
      endDate.setDate(date.getDate() + days);
      dend.html(formattedDate(endDate));
    });
    
    d.keyup(function() {
      endDate = new Date();
      credits = parseInt(s.val()) + x.credits;
      days = parseInt($( this ).val()) + x.days;
      if(days < 1) {
        days = 1;
      }
      val.html(round(credits/days));
      endDate.setDate(date.getDate() + days);
      dend.html(formattedDate(endDate));
    });
});

