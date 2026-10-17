# GoBazaar → Facebook Page auto-post setup

Once this is set up, every time a listing/job/event/business goes **active** on GoBazaar
(whether a regular user posted it, or an admin created/approved it), it's automatically
shared as a post on the GoBazaar Facebook Page. Until you fill these in, posting is simply
skipped — nothing breaks, nothing errors.

You need two values from Facebook: `FACEBOOK_PAGE_ID` and `FACEBOOK_PAGE_ACCESS_TOKEN`.

## Step 1 — Create a Meta for Developers app

1. Go to https://developers.facebook.com/apps and log in with the Facebook account that
   is an **admin of the GoBazaar Page**.
2. Click **Create App** → choose **"Other"** → **"Business"** as the app type.
3. Give it any name (e.g. "GoBazaar Auto-Poster") and finish creating it.

## Step 2 — Add the Facebook Login / Graph API product

1. Inside your new app's dashboard, find **"Graph API Explorer"** under Tools (or go to
   https://developers.facebook.com/tools/explorer/ directly).
2. In the top-right dropdown, select the app you just created.

## Step 3 — Get a Page Access Token

1. In Graph API Explorer, click **"User or Page"** → switch to the GoBazaar **Page**
   (not your personal user).
2. Click **"Generate Access Token"**. A permissions popup appears — approve these two:
   - `pages_manage_posts`
   - `pages_read_engagement`
3. Facebook will generate a **short-lived Page Access Token** (valid ~1 hour). Copy it —
   you'll exchange it for a long-lived one next.

## Step 4 — Exchange it for a long-lived token (valid ~60 days, auto-renewable)

Page tokens generated from a long-lived **user** token effectively never expire in
practice (they get refreshed automatically as long as you, the admin, stay logged into
the app). The simplest reliable path:

1. Get your App ID and App Secret from your app's **Settings → Basic** page.
2. Run this (replace the placeholders), using the short-lived **user** token from Graph
   API Explorer (select "User" instead of "Page" in step 3 to get a user token first):

   ```
   https://graph.facebook.com/v21.0/oauth/access_token?grant_type=fb_exchange_token&client_id=YOUR_APP_ID&client_secret=YOUR_APP_SECRET&fb_exchange_token=YOUR_SHORT_LIVED_USER_TOKEN
   ```

   Open that URL in a browser (or `curl` it) — it returns a long-lived **user** token.

3. Use that long-lived user token to fetch the Page's long-lived **Page** token:

   ```
   https://graph.facebook.com/v21.0/me/accounts?access_token=YOUR_LONG_LIVED_USER_TOKEN
   ```

   This returns a list of Pages you manage, each with its own `access_token` (the Page
   Access Token) and `id` (the Page ID). Find the GoBazaar Page in the list.

## Step 5 — Put it in `.env`

```
FACEBOOK_PAGE_ID=<the "id" from step 4>
FACEBOOK_PAGE_ACCESS_TOKEN=<the "access_token" from step 4>
```

Then clear config cache: `php artisan config:clear`

## Step 6 — Test it

Create a test Listing/Job/Event/Business and set its status to **Active**. Check:
- The GoBazaar Facebook Page — a new post should appear within seconds.
- If nothing appears, check `storage/logs/laravel.log` for a `FacebookPageService` warning
  or error line — it logs the exact Graph API response on failure.

## Notes

- Each record only ever gets posted **once** — if you edit it again afterward, it won't
  re-post (tracked via the `fb_post_id` column).
- A Facebook outage or a bad/expired token never breaks the user's own post — it's only
  ever logged, the listing/job/event/business still saves normally.
