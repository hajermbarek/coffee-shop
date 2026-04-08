// reservation-system.js
// Minimal book & table reservation system

// ========== STORAGE HELPERS ==========
function getBookReservation() {
    const data = localStorage.getItem("cozy_book");
    return data ? JSON.parse(data) : null;
}

function saveBookReservation(book) {
    localStorage.setItem("cozy_book", JSON.stringify(book));
}

function clearBookReservation() {
    localStorage.removeItem("cozy_book");
}

function getTableReservation() {
    const data = localStorage.getItem("cozy_table");
    return data ? JSON.parse(data) : null;
}

function saveTableReservation(table) {
    localStorage.setItem("cozy_table", JSON.stringify(table));
}

function generateCode() {
    return Math.floor(100000 + Math.random() * 900000).toString();
}

// ========== CHECK RESERVATION STATUS ==========
function updateBookStatusDisplay() {
    const bookRes = getBookReservation();
    const statusDiv = document.getElementById("userBookReservationStatus");
    if (!statusDiv) return;
    
    if (bookRes) {
        const expiry = new Date(bookRes.reservedAt);
        expiry.setDate(expiry.getDate() + 7);
        
        if (new Date() > expiry) {
            clearBookReservation();
            statusDiv.innerHTML = '<div style="background:#f8d7da; padding:12px; border-radius:8px;">⚠️ Your book reservation expired. Book a new book.</div>';
        } else {
            statusDiv.innerHTML = `
                <div style="background:#d1e7dd; padding:12px; border-radius:8px;">
                    ✅ You have "<strong>${bookRes.title}</strong>" reserved until ${expiry.toDateString()}<br>
                    🔑 Your code: <strong style="background:#6f4e37; color:white; padding:4px 12px; border-radius:20px;">${bookRes.code}</strong>
                </div>`;
        }
    } else {
        statusDiv.innerHTML = '<div style="background:#e8d9c5; padding:12px; border-radius:8px;">📖 No active reservation. Pick a book to reserve!</div>';
    }
}

// ========== BOOK RESERVATION ==========
function reserveBook(bookTitle) {
    const existing = getBookReservation();
    if (existing) {
        alert(`You already have "${existing.title}" reserved. Wait 7 days or it will expire.`);
        return false;
    }
    
    const code = generateCode();
    const reservation = {
        title: bookTitle,
        code: code,
        reservedAt: new Date().toISOString()
    };
    saveBookReservation(reservation);
    
    alert(`✅ "${bookTitle}" reserved!\n\nYour code: ${code}\n\nUse this code to book your table. Valid for 7 days.`);
    return true;
}

// ========== CODE VERIFICATION MODAL ==========
function showCodeModal(bookTitle, onSuccess) {
    const modal = document.createElement("div");
    modal.style.cssText = "position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.6); display:flex; justify-content:center; align-items:center; z-index:1000;";
    modal.innerHTML = `
        <div style="background:white; padding:30px; border-radius:20px; max-width:400px; text-align:center;">
            <h3>🔐 Verify Your Book</h3>
            <p>You reserved "<strong>${bookTitle}</strong>"<br>Enter your 6-digit code:</p>
            <input type="text" id="codeInput" maxlength="6" style="width:100%; padding:12px; margin:15px 0; border:1px solid #ddd; border-radius:8px; font-size:16px;">
            <button id="verifyBtn" style="background:#6f4e37; color:white; padding:10px 20px; border:none; border-radius:30px; cursor:pointer;">Verify</button>
            <button id="closeBtn" style="margin-left:10px; padding:10px 20px; cursor:pointer;">Cancel</button>
        </div>
    `;
    document.body.appendChild(modal);
    
    document.getElementById("verifyBtn").onclick = () => {
        const code = document.getElementById("codeInput").value.trim();
        const bookRes = getBookReservation();
        if (bookRes && bookRes.code === code) {
            modal.remove();
            onSuccess();
        } else {
            alert("❌ Invalid code!");
        }
    };
    document.getElementById("closeBtn").onclick = () => modal.remove();
}

// ========== MAIN FUNCTION: Check and Redirect ==========
function checkAndRedirectToTable(bookTitle) {
    const bookRes = getBookReservation();
    
    // Case 1: Already has this book reserved
    if (bookRes && bookRes.title === bookTitle) {
        showCodeModal(bookTitle, () => {
            window.location.href = "table-booking.html?book=" + encodeURIComponent(bookTitle);
        });
        return;
    }
    
    // Case 2: No reservation - reserve now then go to table
    if (!bookRes) {
        if (reserveBook(bookTitle)) {
            window.location.href = "table-booking.html?book=" + encodeURIComponent(bookTitle);
        }
        return;
    }
    
    // Case 3: Has different book reserved
    if (bookRes && bookRes.title !== bookTitle) {
        alert(`You already have "${bookRes.title}" reserved. You can only have one book at a time.`);
    }
}

// ========== TABLE BOOKING PAGE LOGIC ==========
function initTableBookingPage() {
    const urlParams = new URLSearchParams(window.location.search);
    const bookTitle = urlParams.get("book");
    
    if (!bookTitle) {
        document.body.innerHTML = "<p style='text-align:center; margin-top:50px;'>No book selected. <a href='books.html'>Go back</a></p>";
        return;
    }
    
    const bookInfoDiv = document.getElementById("bookInfo");
    if (bookInfoDiv) bookInfoDiv.innerHTML = `<strong>📖 ${bookTitle}</strong> (will be ready for you)`;
    
    const form = document.getElementById("tableBookingForm");
    if (form) {
        form.onsubmit = (e) => {
            e.preventDefault();
            
            const date = document.getElementById("reserveDate").value;
            const time = document.getElementById("reserveTime").value;
            const people = document.getElementById("peopleCount").value;
            const area = document.getElementById("areaSelect").value;
            const drink = document.getElementById("drinkOrder").value;
            const comments = document.getElementById("comments").value;
            
            if (!date || !time) {
                alert("Please select date and time");
                return;
            }
            
            const tableRes = {
                book: bookTitle,
                date: date,
                time: time,
                people: people,
                area: area,
                drink: drink,
                comments: comments,
                bookedAt: new Date().toISOString()
            };
            saveTableReservation(tableRes);
            
            alert(`✅ Table booked!\n\n📅 ${date} at ${time}\n👥 ${people} people\n🍽️ ${area}\n☕ ${drink}\n\nYour book "${bookTitle}" will be waiting.`);
            window.location.href = "confirmation.html";
        };
    }
}

// ========== CONFIRMATION PAGE ==========
function showConfirmation() {
    const table = getTableReservation();
    
    if (!table) {
        document.body.innerHTML = "<p style='text-align:center; margin-top:50px;'>No reservation found. <a href='books.html'>Book a book</a></p>";
        return;
    }
    
    const confirmDiv = document.getElementById("confirmDetails");
    if (confirmDiv) {
        confirmDiv.innerHTML = `
            <div style="background:#f9f3e8; padding:20px; border-radius:12px;">
                <p>📚 <strong>Book:</strong> ${table.book}</p>
                <p>📅 <strong>Date:</strong> ${table.date}</p>
                <p>⏰ <strong>Time:</strong> ${table.time}</p>
                <p>👥 <strong>People:</strong> ${table.people}</p>
                <p>🍽️ <strong>Area:</strong> ${table.area}</p>
                <p>☕ <strong>Order:</strong> ${table.drink}</p>
                <p>📝 <strong>Notes:</strong> ${table.comments || "None"}</p>
                <hr>
                <p>✨ Your book is reserved in the Quiet Section. Come anytime during your slot!</p>
            </div>
        `;
    }
}