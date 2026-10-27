<style>


  .ck-content,
  .page-main-content,
  .prose {
    display: block;
    width: 100%;
    clear: both;
    color: #374151;
    font-size: 0.9375rem;
    line-height: 1.75;
    word-break: break-word;
  }

  /* Clearfix so floated images/elements never collapse parent height */
  .ck-content::before,
  .ck-content::after,
  .page-main-content::before,
  .page-main-content::after,
  .prose::before,
  .prose::after {
    content: "";
    display: table;
    clear: both;
  }

  .ck-content *,
  .page-main-content *,
  .prose * {
    box-sizing: border-box;
  }

  /* --------------------------------------------------------------------------
     1. CKEditor Tables Formatting (Border, Zebra, Padding, Responsive Scroll)
     -------------------------------------------------------------------------- */
  .ck-content figure.table,
  .page-main-content figure.table,
  .prose figure.table,
  figure.table {
    display: block;
    width: 100%;
    max-width: 100%;
    margin: 1.75rem 0;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    border-radius: 0.75rem;
  }

  .ck-content table,
  .page-main-content table,
  .prose table,
  figure.table > table,
  table.table {
    width: 100%;
    border-collapse: collapse;
    border-spacing: 0;
    border: 1px solid #d1d5db;
    background-color: #ffffff;
    margin: 1rem 0;
    font-size: 0.875rem;
    line-height: 1.55;
    border-radius: 0.5rem;
    overflow: hidden;
  }

  .ck-content table th,
  .page-main-content table th,
  .prose table th,
  figure.table > table th,
  table.table th {
    background-color: #f1f5f9;
    color: #051650;
    font-weight: 700;
    font-size: 0.875rem;
    text-transform: none;
    letter-spacing: normal;
    padding: 0.75rem 1rem;
    border: 1px solid #d1d5db;
    text-align: left;
    vertical-align: middle;
  }

  .ck-content table td,
  .page-main-content table td,
  .prose table td,
  figure.table > table td,
  table.table td {
    padding: 0.75rem 1rem;
    border: 1px solid #e5e7eb;
    font-size: 0.875rem;
    line-height: 1.55;
    color: #374151;
    vertical-align: top;
  }

  .ck-content table tr:nth-child(even) td,
  .page-main-content table tr:nth-child(even) td,
  .prose table tr:nth-child(even) td,
  figure.table > table tr:nth-child(even) td,
  table.table-striped tbody tr:nth-child(even) td {
    background-color: #f9fafb;
  }

  .ck-content table tr:hover td,
  .page-main-content table tr:hover td,
  .prose table tr:hover td {
    background-color: #f3f4f6;
  }

  /* --------------------------------------------------------------------------
     2. Headings & Typography
     -------------------------------------------------------------------------- */
  .ck-content h1, .page-main-content h1, .prose h1 {
    font-size: 1.875rem;
    font-weight: 700;
    color: #051650;
    margin-top: 2rem;
    margin-bottom: 1rem;
    line-height: 1.3;
  }

  .ck-content h2, .page-main-content h2, .prose h2 {
    font-size: 1.5rem;
    font-weight: 700;
    color: #051650;
    margin-top: 1.75rem;
    margin-bottom: 0.85rem;
    line-height: 1.35;
  }

  .ck-content h3, .page-main-content h3, .prose h3 {
    font-size: 1.25rem;
    font-weight: 700;
    color: #051650;
    margin-top: 1.5rem;
    margin-bottom: 0.75rem;
    line-height: 1.4;
  }

  .ck-content h4, .page-main-content h4, .prose h4 {
    font-size: 1.125rem;
    font-weight: 600;
    color: #051650;
    margin-top: 1.25rem;
    margin-bottom: 0.5rem;
    line-height: 1.4;
  }

  .ck-content h5, .page-main-content h5, .prose h5,
  .ck-content h6, .page-main-content h6, .prose h6 {
    font-size: 1rem;
    font-weight: 600;
    color: #1f2937;
    margin-top: 1rem;
    margin-bottom: 0.5rem;
    line-height: 1.4;
  }

  .ck-content p, .page-main-content p, .prose p {
    margin-top: 0;
    margin-bottom: 1.25rem;
    line-height: 1.75;
    color: #374151;
  }

  .ck-content strong, .page-main-content strong, .prose strong,
  .ck-content b, .page-main-content b, .prose b {
    font-weight: 700;
    color: #111827;
  }

  .ck-content em, .page-main-content em, .prose em,
  .ck-content i, .page-main-content i, .prose i {
    font-style: italic;
  }

  .ck-content u, .page-main-content u, .prose u {
    text-decoration: underline;
  }

  .ck-content s, .page-main-content s, .prose s,
  .ck-content del, .page-main-content del, .prose del {
    text-decoration: line-through;
  }

  .ck-content sub, .page-main-content sub, .prose sub {
    vertical-align: sub;
    font-size: smaller;
  }

  .ck-content sup, .page-main-content sup, .prose sup {
    vertical-align: super;
    font-size: smaller;
  }

  /* --------------------------------------------------------------------------
     3. Lists (Fix Tailwind CSS Reset removing bullets and numbers)
     -------------------------------------------------------------------------- */
  .ck-content ul, .page-main-content ul, .prose ul {
    list-style-type: disc;
    margin: 1rem 0 1.25rem 1.75rem;
    padding-left: 0.5rem;
  }

  .ck-content ul ul, .page-main-content ul ul, .prose ul ul {
    list-style-type: circle;
    margin-top: 0.35rem;
    margin-bottom: 0.35rem;
  }

  .ck-content ul ul ul, .page-main-content ul ul ul, .prose ul ul ul {
    list-style-type: square;
  }

  .ck-content ol, .page-main-content ol, .prose ol {
    list-style-type: decimal;
    margin: 1rem 0 1.25rem 1.75rem;
    padding-left: 0.5rem;
  }

  .ck-content ol ol, .page-main-content ol ol, .prose ol ol {
    list-style-type: lower-alpha;
    margin-top: 0.35rem;
    margin-bottom: 0.35rem;
  }

  .ck-content li, .page-main-content li, .prose li {
    display: list-item;
    margin-bottom: 0.5rem;
    line-height: 1.75;
    color: #374151;
  }

  .ck-content li > p, .page-main-content li > p, .prose li > p {
    margin: 0;
    display: inline;
  }

  /* --------------------------------------------------------------------------
     4. Images, Figures & Media Embeds
     -------------------------------------------------------------------------- */
  .ck-content img,
  .page-main-content img,
  .prose img,
  .ck-content figure,
  .page-main-content figure,
  .prose figure,
  .ck-content video,
  .page-main-content video,
  .prose video,
  .ck-content iframe,
  .page-main-content iframe,
  .prose iframe {
    max-width: 100%;
    height: auto;
  }

  .ck-content figure.image,
  .page-main-content figure.image,
  .prose figure.image {
    display: table;
    margin: 1.5rem auto;
    clear: both;
    text-align: center;
  }

  .ck-content figure.image img,
  .page-main-content figure.image img,
  .prose figure.image img {
    display: block;
    margin: 0 auto;
    border-radius: 0.5rem;
  }

  .ck-content figure.image figcaption,
  .page-main-content figure.image figcaption,
  .prose figure.image figcaption {
    display: table-caption;
    caption-side: bottom;
    text-align: center;
    font-size: 0.875rem;
    font-style: italic;
    color: #6b7280;
    margin-top: 0.5rem;
    padding: 0 0.5rem;
  }

  /* CKEditor 5 image alignment classes */
  .ck-content .image-style-side,
  .page-main-content .image-style-side,
  .prose .image-style-side {
    float: right;
    max-width: 50%;
    margin-left: 1.5rem;
    margin-bottom: 1rem;
  }

  .ck-content .image-style-align-left,
  .page-main-content .image-style-align-left,
  .prose .image-style-align-left {
    float: left;
    margin-right: 1.5rem;
    margin-bottom: 1rem;
  }

  .ck-content .image-style-align-right,
  .page-main-content .image-style-align-right,
  .prose .image-style-align-right {
    float: right;
    margin-left: 1.5rem;
    margin-bottom: 1rem;
  }

  .ck-content .image-style-align-center,
  .page-main-content .image-style-align-center,
  .prose .image-style-align-center {
    margin-left: auto;
    margin-right: auto;
    text-align: center;
  }

  .ck-content .image-style-block-align-left,
  .page-main-content .image-style-block-align-left,
  .prose .image-style-block-align-left {
    margin-right: auto;
    margin-left: 0;
  }

  .ck-content .image-style-block-align-right,
  .page-main-content .image-style-block-align-right,
  .prose .image-style-block-align-right {
    margin-left: auto;
    margin-right: 0;
  }

  @media (max-width: 767px) {
    .ck-content .image-style-side,
    .page-main-content .image-style-side,
    .prose .image-style-side,
    .ck-content .image-style-align-left,
    .page-main-content .image-style-align-left,
    .prose .image-style-align-left,
    .ck-content .image-style-align-right,
    .page-main-content .image-style-align-right,
    .prose .image-style-align-right {
      float: none;
      max-width: 100%;
      margin-left: auto;
      margin-right: auto;
      clear: both;
    }
  }

  /* --------------------------------------------------------------------------
     5. Blockquotes, Code, Pre, Links & Text Alignments
     -------------------------------------------------------------------------- */
  .ck-content blockquote,
  .page-main-content blockquote,
  .prose blockquote {
    border-left: 4px solid #051650;
    background-color: #f8fafc;
    padding: 1rem 1.25rem;
    margin: 1.5rem 0;
    color: #4b5563;
    font-style: italic;
    border-radius: 0 0.5rem 0.5rem 0;
  }

  .ck-content pre,
  .page-main-content pre,
  .prose pre {
    background-color: #0f172a;
    color: #f8fafc;
    padding: 1rem;
    border-radius: 0.5rem;
    overflow-x: auto;
    font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
    font-size: 0.875rem;
    margin: 1.5rem 0;
  }

  .ck-content code,
  .page-main-content code,
  .prose code {
    background-color: #f1f5f9;
    padding: 0.2rem 0.4rem;
    border-radius: 0.25rem;
    font-size: 0.875em;
    font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
    color: #e11d48;
  }

  .ck-content a,
  .page-main-content a,
  .prose a {
    color: #2563eb;
    text-decoration: underline;
    text-underline-offset: 3px;
    transition: color 0.15s ease;
  }

  .ck-content a:hover,
  .page-main-content a:hover,
  .prose a:hover {
    color: #1d4ed8;
  }

  /* Alignment utilities */
  .ck-content .text-left, .page-main-content .text-left, .prose .text-left,
  .ck-content [style*="text-align: left"], .page-main-content [style*="text-align: left"], .prose [style*="text-align: left"],
  .ck-content [style*="text-align:left"], .page-main-content [style*="text-align:left"], .prose [style*="text-align:left"] {
    text-align: left;
  }

  .ck-content .text-center, .page-main-content .text-center, .prose .text-center,
  .ck-content [style*="text-align: center"], .page-main-content [style*="text-align: center"], .prose [style*="text-align: center"],
  .ck-content [style*="text-align:center"], .page-main-content [style*="text-align:center"], .prose [style*="text-align:center"] {
    text-align: center;
  }

  .ck-content .text-right, .page-main-content .text-right, .prose .text-right,
  .ck-content [style*="text-align: right"], .page-main-content [style*="text-align: right"], .prose [style*="text-align: right"],
  .ck-content [style*="text-align:right"], .page-main-content [style*="text-align:right"], .prose [style*="text-align:right"] {
    text-align: right;
  }

  .ck-content .text-justify, .page-main-content .text-justify, .prose .text-justify,
  .ck-content [style*="text-align: justify"], .page-main-content [style*="text-align: justify"], .prose [style*="text-align: justify"],
  .ck-content [style*="text-align:justify"], .page-main-content [style*="text-align:justify"], .prose [style*="text-align:justify"] {
    text-align: justify;
  }

  /* Font size utilities */
  .ck-content .text-tiny, .page-main-content .text-tiny, .prose .text-tiny { font-size: 0.75rem; }
  .ck-content .text-small, .page-main-content .text-small, .prose .text-small { font-size: 0.875rem; }
  .ck-content .text-big, .page-main-content .text-big, .prose .text-big { font-size: 1.25rem; }
  .ck-content .text-huge, .page-main-content .text-huge, .prose .text-huge { font-size: 1.5rem; }
</style>

<script>
  // Defensive helper: Wrap any bare table inside content containers with figure.table for mobile responsiveness
  document.addEventListener('DOMContentLoaded', function () {
    var containers = document.querySelectorAll('.ck-content, .page-main-content, .prose');
    containers.forEach(function (cnt) {
      var tables = cnt.querySelectorAll('table');
      tables.forEach(function (tbl) {
        if (!tbl.closest('figure.table')) {
          var fig = document.createElement('figure');
          fig.className = 'table';
          tbl.parentNode.insertBefore(fig, tbl);
          fig.appendChild(tbl);
        }
      });
    });
  });
</script>
