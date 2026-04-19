(function(){const s=document.getElementById("qrInput"),r=document.getElementById("previewBtn"),i=document.getElementById("checkInBtn"),a=document.getElementById("statusMessage"),d=document.getElementById("resultSection"),g=document.getElementById("recentCheckIns");let o=[];s&&s.focus(),r&&r.addEventListener("click",async()=>{const e=s.value.trim();if(!e){c("Vui lòng nhập mã QR","error");return}try{const t=await(await fetch(window.qrRoutes.preview,{method:"POST",headers:{"Content-Type":"application/json","X-CSRF-TOKEN":document.querySelector('meta[name="csrf-token"]').content},body:JSON.stringify({qr_code:e})})).json();t.success?(u(t.data,"preview"),c("","clear")):(c(t.message,"error"),p())}catch(n){c("Lỗi kết nối: "+n.message,"error")}}),i&&i.addEventListener("click",async()=>{const e=s.value.trim();if(!e){c("Vui lòng nhập mã QR","error");return}if(confirm("Xác nhận check-in?"))try{const t=await(await fetch(window.qrRoutes.checkIn,{method:"POST",headers:{"Content-Type":"application/json","X-CSRF-TOKEN":document.querySelector('meta[name="csrf-token"]').content},body:JSON.stringify({qr_code:e})})).json();t.success?(u(t.data,"checked"),c(t.message,"success"),m(t.data),s.value="",s.focus()):c(t.message,"error")}catch(n){c("Lỗi kết nối: "+n.message,"error")}}),s&&s.addEventListener("keypress",e=>{e.key==="Enter"&&(e.preventDefault(),i.click())});function u(e,n){const t=n==="checked"?'<span class="badge bg-success">✓ Đã check-in</span>':n==="preview"&&e.qr_status==="checked"?'<span class="badge bg-secondary">Đã check-in trước đó</span>':'<span class="badge bg-warning text-dark">Chưa check-in</span>';d.innerHTML=`
            <div class="booking-info">
                <p><strong>Booking ID:</strong> #${e.booking_id} ${t}</p>
                <p><strong>Khách hàng:</strong> ${e.customer_name}</p>
                <p><strong>Phim:</strong> ${e.movie_title}</p>
                <p><strong>Suất chiếu:</strong> ${e.show_date} - ${e.show_time}</p>
                <p><strong>Ghế:</strong><br>
                    ${e.seats.map(l=>`<span class="seat-badge">${l}</span>`).join("")}
                </p>
                ${e.checked_at?`<p><strong>Thời gian check-in:</strong> ${e.checked_at}</p>`:""}
            </div>
        `}function c(e,n){if(n==="clear"){a.innerHTML="";return}const t=n==="success"?"status-success":n==="error"?"status-error":"status-info";a.innerHTML=`<div class="${t}">${e}</div>`}function p(){d.innerHTML='<p class="text-muted text-center">Quét mã QR để xem thông tin</p>'}function m(e){o.unshift(e),o.length>10&&o.pop(),g.innerHTML=o.map(n=>`
            <div class="recent-checkin-item px-3">
                <small>
                    <strong>#${n.booking_id}</strong> - ${n.customer_name} - 
                    ${n.movie_title} - 
                    Ghế: ${n.seats.join(", ")} -
                    <span class="text-success">${n.checked_at}</span>
                </small>
            </div>
        `).join("")}if(document.getElementById("reader")){let e=function(t,l){s&&s.value!==t&&(s.value=t,r&&r.click())},n=function(t){};var h=e,f=n;typeof Html5QrcodeScanner<"u"?new Html5QrcodeScanner("reader",{fps:10,qrbox:{width:250,height:250}},!1).render(e,n):console.error("Html5QrcodeScanner library not found")}})();
