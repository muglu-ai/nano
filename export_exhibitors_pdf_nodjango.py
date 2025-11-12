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


def ensure_packages() -> None:
    to_install: List[str] = []

    # Try imports; enqueue pip install if any missing
    try:
        import weasyprint  # noqa: F401
    except Exception:
        to_install.append(REQUIRED["weasyprint"])

    try:
        import mysql.connector  # noqa: F401
    except Exception:
        to_install.append(REQUIRED["mysql.connector"])

    try:
        import dotenv  # noqa: F401
    except Exception:
        to_install.append(REQUIRED["dotenv"])

    if to_install:
        cmd = [sys.executable, "-m", "pip", "install", "--quiet"] + to_install
        completed = subprocess.run(cmd, capture_output=True, text=True)
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


def first_nonempty(data: Dict[str, Any], keys: List[str]) -> str:
    for k in keys:
        if k in data and data[k] is not None:
            s = str(data[k]).strip()
            if s:
                return s
    return ""


def clean_text(text: str) -> str:
    return text.replace("\\r\\n", " ").replace("\r\n", " ").strip()


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
            @page { size: A4; margin: 8mm; }
            body { font-family: Arial, sans-serif; font-size: 0.7rem; margin: 0; padding: 0; }
            .content { height: 230mm; display: flex; flex-direction: column; justify-content: space-between; margin: 0 auto; padding: 6px; }
            .exhibitor { height: 50%; page-break-inside: avoid; border-top: 1px solid #ddd; padding-top: 12px; }
            .exhibitor1 { height: 50%; page-break-inside: avoid; padding-top: 13px; }
            h1 { font-size: 10px; text-align: center; margin: 0 0 5px 0; }
            table { width: 100%; border-collapse: collapse; }
            td { padding: 2px 4px; word-break: break-word; vertical-align: top; }
            th { padding: 2px 4px; text-align: left; font-weight: bold; vertical-align: top; }
            .header { padding-bottom: 10px; }
            .header img { width: 100%; }
            .profile { line-height: 1.5; text-align: justify; }
            .page-number1 { text-align:center; display:block; margin-top:40px; }
            .muted { color: #666; }
        </style>
    </head>
    <body>
    """)

    total_pages = ceil(len(rows) / 2) if rows else 0

    for i in range(0, len(rows), 2):
        current_page = (i // 2) + 1

        parts.append("""
        <div class="content">
            <div class="header">
                <img src="https://tgs2024.org/admin/header-bg-2.png" alt="Header Image">
            </div>
        """)

        for j in range(2):
            if i + j >= len(rows):
                break

            row = rows[i + j]

            company_name = first_nonempty(row, ["company_name", "fascia_name"])
            sector = first_nonempty(row, ["sector"])
            country = first_nonempty(row, ["country"])
            state = first_nonempty(row, ["state"])
            city = first_nonempty(row, ["city"])
            zip_code = first_nonempty(row, ["zip_code", "zip"])

            contact_person = first_nonempty(row, ["contact_person"])
            designation = first_nonempty(row, ["designation"])
            email = first_nonempty(row, ["email"])
            phone = first_nonempty(row, ["phone", "telPhone"])

            address = first_nonempty(row, ["address"])
            if not address:
                address = ", ".join([p for p in [city.title() if city else "", state.title() if state else "", country.title() if country else ""] if p])
                if zip_code:
                    address = f"{address} {zip_code}".strip()

            description = first_nonempty(row, ["description"])
            website = first_nonempty(row, ["website"])

            category = first_nonempty(row, ["category"])
            is_startup = category.lower() == "startup" if category else False

            block_class = "exhibitor1" if j == 0 else "exhibitor"

            parts.append(f"""
            <div class="{block_class}">
                <h1>{escape((company_name or "N/A").upper())}</h1>
                {'<p style="text-align:center;"><em>(Startup)</em></p>' if is_startup else ''}
                <table>
                    {"<tr><th>Sector</th><th>:</th><td>" + escape(sector) + "</td></tr>" if sector else ""}
                    <tr><th>Contact</th><th>:</th><td>{escape(contact_person or "N/A")}</td></tr>
                    <tr><th>Designation</th><th>:</th><td>{escape(designation or "N/A")}</td></tr>
                    <tr><th>Mobile</th><th>:</th><td>{escape(phone or "N/A")}</td></tr>
                    <tr><th>E-mail</th><th>:</th><td>{escape(email or "N/A")}</td></tr>
                    <tr><th>Address</th><th>:</th><td>{escape(address or "N/A")}</td></tr>
                    {"<tr><th>Website</th><th>:</th><td>" + escape(website) + "</td></tr>" if website else ""}
                    <tr><th><br>Profile:</th><th> </th></tr>
                    <tr><td colspan="3" class="profile">{escape(description or "N/A")}</td></tr>
                </table>
            </div>
            """)

        parts.append(f'<span class="page-number1">{current_page} of {total_pages}</span>')
        parts.append("</div>")

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
    table_name = os.getenv("EXHIBITORS_TABLE", "").strip()
    output_dir = None

    args = sys.argv[1:]
    for idx, arg in enumerate(args):
        if arg == "--env" and idx + 1 < len(args):
            env_path = args[idx + 1]
        if arg == "--table" and idx + 1 < len(args):
            table_name = args[idx + 1]
        if arg == "--out" and idx + 1 < len(args):
            output_dir = args[idx + 1]

    ensure_packages()
    load_env(env_path)

    if not table_name:
        # Fall back to env or a generic default; user can override via --table
        table_name = os.getenv("DB_TABLE", "").strip() or "exhibitors_info"

    rows = fetch_rows(table_name)
    html = build_html(rows)
    pdf_path = write_pdf(html, output_dir)
    print(f"PDF generated: {pdf_path}")


if __name__ == "__main__":
    main()

