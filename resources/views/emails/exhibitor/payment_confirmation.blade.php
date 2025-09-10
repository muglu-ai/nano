@component('mail::message')

@php
    $c_name = $c_name ?? 'Exhibitor';
    $email = $email ?? ''; 
    $url = $url ?? 'https://portal.semiconindia.org/';
@endphp
Dear {{ $c_name ?? 'Exhibitor' }},

Thank you for your payment for **SEMICON India 2025**! We're excited to have you on board.

### Important Updates:

1. **Payment Receipt**: You can view your payment receipt by logging into your account at [this link] https://portal.semiconindia.org/.
2. **Exhibitor Badge Information & Additional Services**: A new tab for extra requirements is now available in your portal. Please fill in the exhibitor badge information and order any additional services before the deadline.

---

### Contact for Booth Construction & Extra Orders:

For any queries regarding booth construction or extra orders, please reach out to  
📧 **fabrication@mmactiv.com**

---

### 🔧 Action Items in Exhibitor Portal:

1. **Invite/Add Exhibitors** – Manage your team with ease.  
2. **Order Extra Requirements** – Enhance your stall experience.  
3. **Access Documents** – Find all relevant documents in one place.  
4. **Manage Co-Exhibitors** – Keep track of your collaborations.

---

### 🔐 Login Details:

- **Registered Email**: {{ $email }}  
- **Exhibitor Portal**: [Click here]({{ $url }})

If you have any questions or need assistance, feel free to reach out.

Best regards,  
**Your Name**  
@endcomponent
