import os
import sys
import subprocess
import datetime
from typing import Any, Dict, List, Optional


REQUIRED = {
    "weasyprint": "weasyprint",
    "mysql.connector": "mysql-connector-python",
    "dotenv": "python-dotenv",
}

# Toggle to force hardcoded DB credentials (set to True to bypass .env)
USE_HARDCODED_DB: bool = True

# Hardcoded MySQL credentials (update these values)
HARDCODED_DB = {
    "host": "95.216.2.164",
    "port": 3306,
    "database": "btsblnl265_asd1d_portal",
    "user": "btsblnl265_asd1d_bengaluruite",
    "password": 'Disl#vhfj#Af#DhW65'
}


def ensure_packages() -> None:
    to_install: List[str] = []

    # Try imports; enqueue pip install if any missing
    try:
        import weasyprint  # noqa: F401
    except Exception:
        # Python 3.6 and older Pango/Cairo stacks often require older WeasyPrint
        if sys.version_info[:2] <= (3, 6):
            to_install.append("weasyprint==52.5")
        else:
            to_install.append(REQUIRED["weasyprint"])

    try:
        import mysql.connector  # noqa: F401
    except Exception:
        # Python 3.6 compatibility: pin mysql-connector-python to a version that supports 3.6
        if sys.version_info[:2] <= (3, 6):
            to_install.append("mysql-connector-python==8.0.28")
        else:
            to_install.append(REQUIRED["mysql.connector"])

    try:
        import dotenv  # noqa: F401
    except Exception:
        to_install.append(REQUIRED["dotenv"])

    if to_install:
        cmd = [sys.executable, "-m", "pip", "install", "--quiet", "--upgrade", "--force-reinstall"] + to_install
        # Python 3.6 compatibility: no capture_output/text args
        completed = subprocess.run(
            cmd,
            stdout=subprocess.PIPE,
            stderr=subprocess.PIPE,
            universal_newlines=True,
        )
        if completed.returncode != 0:
            raise RuntimeError(
                f"Failed to install packages: {to_install}\nstdout:\n{completed.stdout}\nstderr:\n{completed.stderr}"
            )


def load_env(env_path: Optional[str]) -> None:
    from dotenv import load_dotenv

    if env_path:
        load_dotenv(dotenv_path=env_path, override=True)
    else:
        load_dotenv(override=True)


def get_db_config() -> Dict[str, Any]:
    if USE_HARDCODED_DB:
        return HARDCODED_DB

    # Expected envs
    db_connection = os.getenv("DB_CONNECTION", "").strip().lower()
    if db_connection and db_connection != "mysql":
        raise ValueError("Only MySQL is supported. Set DB_CONNECTION=mysql.")

    config = {
        "host": os.getenv("DB_HOST", "").strip(),
        "port": int(os.getenv("DB_PORT", "3306")),
        "database": os.getenv("DB_DATABASE", "").strip(),
        "user": os.getenv("DB_USERNAME", "").strip(),
        "password": os.getenv("DB_PASSWORD", "").strip(),
    }
    missing = [k for k, v in config.items() if not v and k != "port"]
    if missing:
        raise ValueError(f"Missing required DB envs: {', '.join(missing)}")
    return config


def fetch_rows(table_name: str) -> List[Dict[str, Any]]:
    import mysql.connector
    from mysql.connector import Error

    cfg = get_db_config()
    conn = None
    try:
        conn = mysql.connector.connect(
            host=cfg["host"],
            port=cfg["port"],
            database=cfg["database"],
            user=cfg["user"],
            password=cfg["password"],
        )
        cursor = conn.cursor(dictionary=True)
        cursor.execute(f"SELECT * FROM {table_name}")
        rows = cursor.fetchall()
        return rows or []
    except Error as e:
        raise RuntimeError(f"MySQL error: {e}") from e
    finally:
        try:
            if conn and conn.is_connected():
                conn.close()
        except Exception:
            pass


def fetch_exhibitors(event_id: Optional[int] = None) -> List[Dict[str, Any]]:
    import mysql.connector
    from mysql.connector import Error

    cfg = get_db_config()
    conn = None
    try:
        conn = mysql.connector.connect(
            host=cfg["host"],
            port=cfg["port"],
            database=cfg["database"],
            user=cfg["user"],
            password=cfg["password"],
        )
        cursor = conn.cursor(dictionary=True)

        query = """
        SELECT
            a.id AS a_id,
            a.company_name AS a_company_name,
            a.fascia_name AS a_fascia_name,
            a.address AS a_address,
            a.postal_code AS a_postal_code,
            a.company_email AS a_company_email,
            a.website AS a_website,
            a.landline AS a_landline,
            a.main_product_category AS a_main_product_category,
            a.comments AS a_comments,
            a.boothDescription AS a_booth_description,
            ei.*,
            ec.ec_salutation,
            ec.ec_first_name,
            ec.ec_last_name,
            ec.ec_job_title,
            ec.ec_email
        FROM applications a
        LEFT JOIN exhibitors_info ei ON ei.application_id = a.id
        LEFT JOIN (
            SELECT t.application_id,
                   t.salutation AS ec_salutation,
                   t.first_name AS ec_first_name,
                   t.last_name AS ec_last_name,
                   t.job_title AS ec_job_title,
                   t.email AS ec_email,
                   t.id AS ec_id
            FROM event_contacts t
            INNER JOIN (
                SELECT application_id, MIN(id) AS id
                FROM event_contacts
                GROUP BY application_id
            ) x ON x.application_id = t.application_id AND x.id = t.id
        ) ec ON ec.application_id = a.id
        """
        params: List[Any] = []
        if event_id is not None:
            query += " WHERE a.event_id = %s"
            params.append(event_id)

        cursor.execute(query, params)
        rows = cursor.fetchall()
        return rows or []
    except Error as e:
        raise RuntimeError(f"MySQL error: {e}") from e
    finally:
        try:
            if conn and conn.is_connected():
                conn.close()
        except Exception:
            pass


def get_display_company_name(row: Dict[str, Any]) -> str:
    candidates = [
        # row.get("fascia_name"),
        # row.get("company_name"),
        # row.get("a_fascia_name"),
        row.get("a_company_name"),
    ]
    for c in candidates:
        if c is not None:
            s = str(c).strip()
            if s:
                return s
    return ""


def first_nonempty(data: Dict[str, Any], keys: List[str]) -> str:
    for k in keys:
        if k in data and data[k] is not None:
            s = str(data[k]).strip()
            if s:
                return s
    return ""


def clean_text(text: str) -> str:
    return text.replace("\\r\\n", " ").replace("\r\n", " ").strip()


def has_display_value(value: Optional[str]) -> bool:
    if value is None:
        return False
    s = str(value).strip()
    if not s:
        return False
    return s.lower() not in ("n/a", "na")


def build_html(rows: List[Dict[str, Any]]) -> str:
    from html import escape
    from math import ceil

    parts: List[str] = []
    parts.append("""
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <style>
            @page {
                size: 100mm 240mm; /* Custom page size */
                margin: 8mm;
            }
            body {
                font-family: Arial, sans-serif;
                font-size: 0.56rem;
                margin: 0;
                padding: 0;
            }
            .content {
                height: 230mm; /* Available content height */
                display: flex;
                flex-direction: column;
                justify-content: space-between;
                margin: 0 auto;
                padding: 6px;
            }
            .exhibitor {
                height: 50%; /* Each exhibitor takes half of the page */
                page-break-inside: avoid; /* Keep each exhibitor on one page */
                border-top: 1px solid #ddd;
                padding-top: 12px;
            }
            .exhibitor1 {
                height: 50%; /* Each exhibitor takes half of the page */
                page-break-inside: avoid; /* Keep each exhibitor on one page */
                padding-top: 13px;
            }
            h1 {
                font-size: 10px;
                text-align: center;
                margin: 0 0 5px 0;
            }
            table { width: 100%; border-collapse: collapse; }
            td { padding: 2px 4px; word-break: break-word; vertical-align: top; }
            th { padding: 2px 4px; text-align: left; font-weight: bold; vertical-align: top; }
            .front-page, .back-page { width: 100%; page-break-after: always; }
            .front-page img, .back-page img { width: 100%; height: 135%; }
            .header{ padding-bottom: 10px; }
            .header img, .footer img { width: 100%; }
            .profile{ line-height: 1.5; text-align: justify; }
            .page-number11 { position: fixed; bottom: 5px; left: 50%; transform: translateX(-50%); z-index: 1000; text-align: center; }
            .page-number1{ text-align:center; display:block; margin-top:40px; }
        </style>
    </head>
    <body>
    """)

    # Sort exhibitors alphabetically by display company name (empty names last)
    rows = sorted(rows, key=lambda r: (not bool(get_display_company_name(r).strip()), get_display_company_name(r).strip().lower()))

    total_pages = ceil(len(rows) / 2) if rows else 0

    for i in range(0, len(rows), 2):
        current_page = (i // 2) + 1

        parts.append("""
        <div class="content">
            <div class="header">
                <img src="https://bengalurutechsummit.com/portal/public/asset/header.jpg" alt="Header Image">
            </div>
        """)

        for j in range(2):
            if i + j >= len(rows):
                break

            row = rows[i + j]

            # Extract fields with precedence:
            # Company name display: exhibitors_info first, then applications
            company_name = get_display_company_name(row)

            # Sector/category
            sector = first_nonempty(row, ["sector", "category", "assoc_nm", "a_main_product_category"])

            # Location and postal
            country = first_nonempty(row, ["country"])
            state = first_nonempty(row, ["state"])
            city = first_nonempty(row, ["city"])
            zip_code = first_nonempty(row, ["zip_code", "zip", "a_postal_code"])

            # Contact details: prefer exhibitors_info, then event_contacts, then applications
            exhibitor_name = " ".join([p for p in [
                first_nonempty(row, ["contact_person"]),
            ] if p]).strip()
            ec_name = " ".join([p for p in [
                first_nonempty(row, ["ec_salutation"]),
                first_nonempty(row, ["ec_first_name"]),
                first_nonempty(row, ["ec_last_name"]),
            ] if p]).strip()

            # Prefer exhibitors_info name, then event_contacts name, then blank
            contact_person = exhibitor_name or ec_name

            # Prefer exhibitors_info job title, then event_contacts, then applications
            designation = first_nonempty(row, ["designation", "ec_job_title"])

            # Prefer exhibitors_info email, then event_contacts, then applications
            email = first_nonempty(row, ["email", "ec_email", "a_company_email"])

            # Prefer exhibitors_info phone, then event_contacts, then applications
            phone = first_nonempty(row, ["phone", "ec_phone", "telPhone", "mobile", "mob", "a_landline"])

            # Address: exhibitors_info first, else applications address + postal
            address = first_nonempty(row, ["address", "address_line_1"])
            if not address:
                base_addr = first_nonempty(row, ["a_address"]) or ""
                address = base_addr.strip()
                if zip_code:
                    address = f"{address} {zip_code}".strip()

            # Description/profile: exhibitors_info first, else applications
            description = first_nonempty(row, ["description", "profile", "a_comments", "a_booth_description"])

            # Website: exhibitors_info first, else applications
            website = first_nonempty(row, ["website", "a_website"])

            # Fix double https
            if website.startswith("https://https://"):
                website = website.replace("https://https://", "https://", 1)

            # Sanitize newlines and replace &Amp;
            def sanitize_text(s: str) -> str:
                if not s:
                    return s
                s = s.replace("\\r\\n", " ")
                s = s.replace("\r\n", " ")
                s = s.replace("&Amp;", "&").replace("&amp;", "&")
                return s.strip()

            designation = sanitize_text(designation)
            description = sanitize_text(description)
            address = sanitize_text(address)

            category = first_nonempty(row, ["category", "assoc_nm"])
            is_startup = (category.lower() == "startup") if category else False

            # Debug print similar to reference
            try:
                print(f"Processing exhibitor {i + j + 1}: {company_name}")
            except Exception:
                pass

            block_class = "exhibitor1" if j == 0 else "exhibitor"

            # Build table rows conditionally; hide header if value is empty or 'N/A'
            table_rows: List[str] = []
            if has_display_value(sector):
                table_rows.append(f"<tr><th>Sector</th><th>:</th><td>{escape(sector)}</td></tr>")
            if has_display_value(contact_person):
                table_rows.append(f"<tr><th>Contact </th><th>:</th><td>{escape(contact_person)}</td></tr>")
            if has_display_value(designation):
                table_rows.append(f"<tr><th>Designation</th><th>:</th><td>{escape(designation)}</td></tr>")
            if has_display_value(phone):
                table_rows.append(f"<tr><th>Mobile</th><th>:</th><td>{escape(phone)}</td></tr>")
            if has_display_value(email):
                table_rows.append(f"<tr><th>E-mail</th><th>:</th><td>{escape(email)}</td></tr>")
            if has_display_value(address):
                table_rows.append(f"<tr><th>Address</th><th>:</th><td>{escape(address)}</td></tr>")
            if has_display_value(website):
                table_rows.append(f"<tr><th>Website</th><th>:</th><td>{escape(website)}</td></tr>")
            if has_display_value(description):
                table_rows.append("<tr><th><br>Profile:</th><th> </th></tr>")
                table_rows.append(f'<tr><td colspan="3" class="profile">{escape(description)}</td></tr>')

            rows_html = "\n".join(table_rows)

            parts.append(f"""
            <div class="{block_class}">
                <h1>{escape((company_name or "N/A").upper())}</h1>
                {'<p style="text-align:center;"><em>(Startup)</em></p>' if is_startup else ''}
                <table>
                    {rows_html}
                </table>
            </div>
            """)

        # Numeric current-page indicator to match sample
        parts.append(f'<span class="page-number1"> {current_page} </span>')
        parts.append('</div>')

    parts.append("""
    </body>
    </html>
    """)

    return "".join(parts)


def write_pdf(html_content: str, output_dir: Optional[str]) -> str:
    from weasyprint import HTML

    out_dir = output_dir or os.path.join(os.path.dirname(os.path.abspath(__file__)), "generated_directory")
    os.makedirs(out_dir, exist_ok=True)

    # Save HTML snapshot
    html_out_path = os.path.join(out_dir, "exhibitor_directory.html")
    with open(html_out_path, "w", encoding="utf-8") as fp:
        fp.write(html_content)

    timestamp = datetime.datetime.now().strftime("%Y%m%d%H%M%S")
    pdf_filename = f"BTS_Exhibitor_Directory_{timestamp}.pdf"
    pdf_path = os.path.join(out_dir, pdf_filename)

    HTML(string=html_content, base_url=out_dir).write_pdf(pdf_path)
    return os.path.abspath(pdf_path)


def main() -> None:
    # Parse simple args: --env, --table, --out
    env_path = None
    table_name = "exhibitors_info"
    output_dir = None
    event_id: Optional[int] = None

    args = sys.argv[1:]
    for idx, arg in enumerate(args):
        if arg == "--env" and idx + 1 < len(args):
            env_path = args[idx + 1]
        if arg == "--table" and idx + 1 < len(args):
            table_name = args[idx + 1]
        if arg == "--out" and idx + 1 < len(args):
            output_dir = args[idx + 1]
        if arg == "--event" and idx + 1 < len(args):
            try:
                event_id = int(args[idx + 1])
            except Exception:
                event_id = None

    ensure_packages()
    load_env(env_path)

    if not table_name:
        # Fall back to env or a generic default; user can override via --table
        table_name = "exhibitors_info"

    # Always fetch exhibitors based on applications, with joins
    rows = fetch_exhibitors(event_id=event_id)
    html = build_html(rows)
    pdf_path = write_pdf(html, output_dir)
    print(f"PDF generated: {pdf_path}")


if __name__ == "__main__":
    main()

