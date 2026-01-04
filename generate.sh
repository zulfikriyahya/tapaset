#!/bin/bash
# generate.sh - Generator Blueprint Otomatis

set -euo pipefail

OUT="draft.md"
ROOT="."

# Exclude patterns untuk Laravel project
EXCLUDE_PATTERNS=(
  # Vendor & Dependencies
  "./vendor/*"
  "./node_modules/*"

  # Laravel Storage & Cache
  "./storage/logs/*"
  "./storage/framework/cache/*"
  "./storage/framework/sessions/*"
  "./storage/framework/views/*"
  "./bootstrap/cache/*"

  # Git & Version Control
  "./.git/*"
  "./.github/*"

  # Build & Compiled
  "./public/build/*"
  "./public/hot"
  "./public/storage"

  # Environment & Config
  "./.env"
  # "./.env.example"
  "./.env.backup"
  "./.env.production"

  # Lock files
  "./composer.lock"
  "./package-lock.json"
  "./pnpm-lock.yaml"
  "./yarn.lock"

  # IDE & Editor
  "./.idea/*"
  "./.vscode/*"
  "./.fleet/*"

  # Testing & Coverage
  "./coverage/*"
  "./.phpunit.result.cache"

  # Documentation generated
  "./docs/api/*"

  # Database
  "./database/*.sqlite"
  "./database/*.db"
  "*.sql"

  # Logs
  "*.log"

  # OS Files
  "./.DS_Store"
  "./Thumbs.db"

  # Script itself
  "./$OUT"
  "./generate.sh"
  "./.generate.sh"
  "./.blueprint"
  "./draft.yaml"

  # Git files
  "./.gitignore"
  "./.gitattributes"

  # Images & Media
  "*.png" "*.jpg" "*.jpeg" "*.webp" "*.ico" "*.gif" "*.svg" "*.avif"
  "*.woff" "*.woff2" "*.ttf" "*.otf" "*.eot"
  "*.mp3" "*.mp4" "*.avi" "*.mov"

  # Laravel Mix/Vite compiled
  "./public/css/app.css"
  "./public/js/app.js"
  "./public/mix-manifest.json"

  # README (opsional, uncomment jika tidak ingin include)
  "./README.md"
)

# Function to determine language for syntax highlighting
lang_for_ext() {
  case "$1" in
    # PHP
    php)   printf "php" ;;
    blade.php) printf "blade" ;;

    # JavaScript/TypeScript
    js)    printf "javascript" ;;
    jsx)   printf "jsx" ;;
    ts)    printf "typescript" ;;
    tsx)   printf "tsx" ;;
    mjs)   printf "javascript" ;;
    cjs)   printf "javascript" ;;
    vue)   printf "vue" ;;

    # Styles
    css)   printf "css" ;;
    scss)  printf "scss" ;;
    sass)  printf "sass" ;;
    less)  printf "less" ;;

    # Config & Data
    json)  printf "json" ;;
    yml|yaml) printf "yaml" ;;
    xml)   printf "xml" ;;
    env)   printf "bash" ;;

    # Documentation
    md)    printf "markdown" ;;
    mdx)   printf "markdown" ;;

    # Shell & Scripts
    sh)    printf "bash" ;;
    bash)  printf "bash" ;;

    # SQL
    sql)   printf "sql" ;;

    # Other
    txt)   printf "text" ;;
    html)  printf "html" ;;
    svg)   printf "xml" ;;
    Dockerfile) printf "dockerfile" ;;
    Makefile)   printf "makefile" ;;

    *)     printf "" ;;
  esac
}

# Clear output file
: > "$OUT"

# Find all files
files=()
while IFS= read -r -d '' f; do
  skip=false
  for pat in "${EXCLUDE_PATTERNS[@]}"; do
    if [[ "$f" == $pat ]]; then
      skip=true
      break
    fi
  done
  $skip && continue
  files+=("$f")
done < <(find "$ROOT" -type f -print0)

if [ "${#files[@]}" -eq 0 ]; then
  echo "Tidak ada file ditemukan untuk diproses."
  exit 0
fi

# Write project header
cat >> "$OUT" << 'EOF'
# Laravel Project Blueprint

EOF

# Group files by top-level directory
declare -A groups
for f in "${files[@]}"; do
  p="${f#./}"
  if [[ "$p" == */* ]]; then
    top="${p%%/*}"
  else
    top="ROOT"
  fi
  rel="./${p}"
  if [ -z "${groups[$top]:-}" ]; then
    groups[$top]="$rel"
  else
    groups[$top]="${groups[$top]}"$'\n'"$rel"
  fi
done

# Sort and write grouped files
IFS=$'\n'
for top in $(printf '%s\n' "${!groups[@]}" | sort -V); do
  # Add directory description
  case "$top" in
    "app")
      printf "## 📁 Directory: %s (Application Core)\n\n" "$top" >> "$OUT"
      printf "Contains models, controllers, services, and business logic.\n\n" >> "$OUT"
      ;;
    "database")
      printf "## 📁 Directory: %s (Database)\n\n" "$top" >> "$OUT"
      printf "Migrations, seeders, and factories.\n\n" >> "$OUT"
      ;;
    "resources")
      printf "## 📁 Directory: %s (Frontend Resources)\n\n" "$top" >> "$OUT"
      printf "Views, CSS, JavaScript, and frontend assets.\n\n" >> "$OUT"
      ;;
    "routes")
      printf "## 📁 Directory: %s (Routes)\n\n" "$top" >> "$OUT"
      printf "Application routing definitions.\n\n" >> "$OUT"
      ;;
    "config")
      printf "## 📁 Directory: %s (Configuration)\n\n" "$top" >> "$OUT"
      printf "Application configuration files.\n\n" >> "$OUT"
      ;;
    "public")
      printf "## 📁 Directory: %s (Public Assets)\n\n" "$top" >> "$OUT"
      printf "Publicly accessible files (entry point).\n\n" >> "$OUT"
      ;;
    "tests")
      printf "## 📁 Directory: %s (Tests)\n\n" "$top" >> "$OUT"
      printf "Unit and feature tests.\n\n" >> "$OUT"
      ;;
    "ROOT")
      printf "## 📁 Directory: Root Files\n\n" >> "$OUT"
      printf "Configuration and setup files in project root.\n\n" >> "$OUT"
      ;;
    *)
      printf "## 📁 Directory: %s\n\n" "$top" >> "$OUT"
      ;;
  esac

  mapfile -t flist < <(printf '%s\n' "${groups[$top]}" | sort -V)

  for file in "${flist[@]}"; do
    case "$file" in
      "./$OUT" | "$OUT" | "./generate.sh" | "generate.sh" | "./.generate.sh") continue ;;
    esac

    filename="$(basename -- "$file")"

    # Handle blade.php extension
    if [[ "$filename" == *.blade.php ]]; then
      ext="blade.php"
    elif [[ "$filename" == *.* ]]; then
      ext="${filename##*.}"
    else
      ext="$filename"
    fi

    lang="$(lang_for_ext "$ext")"

    printf "### 📄 File: \`%s\`\n\n" "$file" >> "$OUT"

    # Add file description for important files
    case "$filename" in
      "composer.json")
        printf "_PHP dependencies and project metadata._\n\n" >> "$OUT"
        ;;
      "package.json")
        printf "_Node.js dependencies and build scripts._\n\n" >> "$OUT"
        ;;
      "artisan")
        printf "_Laravel command-line interface._\n\n" >> "$OUT"
        ;;
      "phpunit.xml")
        printf "_PHPUnit testing configuration._\n\n" >> "$OUT"
        ;;
      "vite.config.js")
        printf "_Vite build tool configuration._\n\n" >> "$OUT"
        ;;
      "tailwind.config.js")
        printf "_Tailwind CSS configuration._\n\n" >> "$OUT"
        ;;
    esac

    if [ -n "$lang" ]; then
      printf '```%s\n' "$lang" >> "$OUT"
    else
      printf '```\n' >> "$OUT"
    fi

    # Remove carriage returns and output file content
    sed 's/\r$//' "$file" >> "$OUT"

    printf '\n```\n\n---\n\n' >> "$OUT"
  done
done
