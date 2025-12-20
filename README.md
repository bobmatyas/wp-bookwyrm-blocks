# Blocks for BookWyrm

Blocks for BookWyrm is a WordPress plugin that adds two blocks to your site's block editor, allowing you to display your reading activity from any BookWyrm instance. The plugin automatically fetches and displays your books with covers, titles, and author information.

## Documentation

### What This Plugin Does

The plugin provides two blocks:
- **BookWyrm - Recently Read**: Displays books you've recently finished reading
- **BookWyrm - Currently Reading**: Displays books you're currently reading

Both blocks automatically pull data from your BookWyrm account and display it on your WordPress site with book covers, titles, and author names.

---

### Installation

#### Step 1: Install the Plugin

1. Go to your WordPress admin dashboard
2. Navigate to **Plugins** → **Add New**
3. Search for "Blocks for BookWyrm"
4. Click **Install Now**
5. Click **Activate**

Alternatively, if you've downloaded the plugin manually:
1. Upload the plugin files to `/wp-content/plugins/blocks-for-bookwyrm/`
2. Go to **Plugins** in your WordPress admin
3. Find "Blocks for BookWyrm" and click **Activate**

#### Step 2: Verify Installation

After activation, you should see two new blocks available in the block editor:
- **BookWyrm - Recently Read**
- **BookWyrm - Currently Reading**

These blocks appear in the **Widgets** category when you search for blocks in the editor.

---

### Using the Blocks

#### Adding a Block to Your Page or Post

1. **Open the Block Editor**: Edit any page or post, or create a new one
2. **Add a Block**: Click the **+** button or type `/` to search for blocks
3. **Search for BookWyrm**: Type "BookWyrm" in the search box
4. **Select a Block**: Choose either:
   - **BookWyrm - Recently Read** (for books you've finished)
   - **BookWyrm - Currently Reading** (for books you're reading now)

### Configuring the Block

After adding a block, you need to configure it with your BookWyrm information:

1. **Select the Block**: Click on the block you just added
2. **Open Block Settings**: Look for the **Settings** panel on the right side of the editor (if it's not visible, click the gear icon in the top right)
3. **Enter Your BookWyrm Username**: 
   - In the **Bookwyrm Username** field, enter your BookWyrm username (without the @ symbol)
   - Example: If your BookWyrm profile is `@johndoe@bookwyrm.social`, enter just `johndoe`
4. **Enter Your BookWyrm Instance**:
   - In the **Bookwyrm Instance** field, enter your BookWyrm instance domain
   - You can enter it with or without `https://` - the plugin will handle it automatically
   - Examples:
     - `bookwyrm.social`
     - `https://bookwyrm.social`
     - `mybookwyrm.example.com`

#### Example Configuration

If your BookWyrm profile URL is `https://bookwyrm.social/user/johndoe`, you would configure:
- **Bookwyrm Username**: `johndoe`
- **Bookwyrm Instance**: `bookwyrm.social`

#### Verifying Your Configuration

After entering your settings, you should see a green message in the editor that says:
- "Displaying recently read books from *[your username]* at *[your instance]*" (for the Recently Read block)
- "Displaying **currently reading** books from *[your username]* at *[your instance]*" (for the Currently Reading block)

If you see a red warning message instead, double-check that both fields are filled in correctly.

**Note**: The block preview in the editor shows placeholder content. Your actual books will appear when you view the published page on the front-end of your site.

---

### Placeholder Selection

When a book doesn't have a cover image available (either because it lacks an ISBN or OpenLibrary doesn't have a cover for that ISBN), the plugin will display a placeholder image instead. You can customize which placeholder style is used:

1. **Select the Block**: Click on the block you've added
2. **Open Block Settings**: In the **Settings** panel on the right side of the editor, scroll down to find the **Placeholder Cover** section
3. **Choose Placeholder Type**: Use the dropdown to select from:
   - **SVG (Simple)**: A simple, clean SVG placeholder image (default)
   - **PNG (Fancy)**: A more detailed PNG placeholder image
4. **Preview**: A preview of the selected placeholder will appear below the dropdown so you can see how it looks

The placeholder you select will be used for all books in that block that don't have cover images available. This setting is saved per block, so you can use different placeholder styles for different blocks if desired.

---

### What Gets Displayed

Each block displays your books with:

- **Book Cover**: Automatically fetched from OpenLibrary using the book's ISBN
- **Book Title**: The title of the book
- **Author Name**: The author's name (prefixed with "by")

The books are displayed in a grid layout, showing multiple books at once.

#### Important Notes About Display

- **Books must have an ISBN**: Only books with an ISBN (ISBN-10 or ISBN-13) will be displayed, as the plugin needs this to fetch book covers
- **Public information only**: The plugin only displays information that is already public on your BookWyrm profile
- **Real-time updates**: The block fetches fresh data from BookWyrm each time the page loads, so your reading activity stays up to date

---

### Troubleshooting

#### Block Shows an Error Message

If you see "⚠️ Sorry, there has been an error fetching the feed" on your published site:

1. **Check your username**: Make sure you entered your BookWyrm username correctly (without the @ symbol)
2. **Check your instance URL**: Verify the instance domain is correct
3. **Verify your BookWyrm profile is public**: The plugin can only access public information
4. **Check your internet connection**: The plugin needs to connect to your BookWyrm instance
5. **Try a different instance**: If you're using a custom BookWyrm instance, make sure it's accessible and supports the public API endpoints

#### No Books Are Displayed

If the block appears but shows no books:

1. **Check your BookWyrm shelves**: Make sure you have books marked as "Read" (for Recently Read block) or "Currently Reading" (for Currently Reading block) on your BookWyrm account
2. **Verify books have ISBNs**: Books without ISBNs won't be displayed
3. **Check your BookWyrm privacy settings**: Ensure your reading activity is set to public visibility

#### Book Covers Don't Appear

If book titles and authors show but covers are missing:

- This usually means the book doesn't have an a cover in OpenLibrary doesn't have a cover for that ISBN
- The plugin will still display the book title and author even without a cover

### Will This Work With Any BookWyrm Instance?

The plugin should work with any BookWyrm instance, though it has been primarily tested with `bookwyrm.social`. If you're using a different instance and encounter issues, it may be due to:
- Instance-specific API differences
- Privacy settings on that instance
- Network connectivity issues

---

## Privacy and Data

### What Data is Shared?

- **With BookWyrm**: The plugin sends your configured username to your BookWyrm instance to retrieve your public reading lists. Only information that is already public on your BookWyrm profile is accessed.
- **With OpenLibrary**: To display book covers, the plugin sends only the ISBN of each book to OpenLibrary.org. No personal information or user data is sent to OpenLibrary.

### Privacy Policies

- **BookWyrm**: Each BookWyrm instance has its own privacy policy. Check your instance's privacy policy for details about how your data is handled.
- **OpenLibrary**: OpenLibrary is a project of Archive.org and is governed by the [Archive.org Terms of Service and Privacy Policy](https://archive.org/about/terms).

---

## Tips and Best Practices

1. **Use both blocks**: Consider adding both blocks to your site - one for currently reading books and one for recently read books
2. **Styling**: The blocks come with default styling, but you can customize their appearance using your theme's CSS or WordPress's block styling options
3. **Keep it updated**: Make sure to keep the plugin updated to the latest version for bug fixes and improvements

---

### Frequently Asked Questions

**Q: Do I need a BookWyrm account to use this plugin?**  
A: Yes, you need an active BookWyrm account with books marked as "Read" or "Currently Reading" on a public shelf.

**Q: Can I display someone else's reading list?**  
A: Yes, as long as their reading lists are set to public visibility on BookWyrm, you can enter their username and instance.

**Q: How often does the block update?**  
A: The block fetches fresh data from BookWyrm each time a page loads, so it's always up to date.

**Q: Can I limit how many books are shown?**  
A: Currently, the plugin displays all books from the first page of results. 

**Q: What happens if a book doesn't have a cover?**  
A: The plugin will display the placeholder image you've selected in the block settings (either SVG or PNG). You can change this setting in the **Placeholder Cover** section of the block settings.

**Q: Will this slow down my site?**  
A: The plugin makes API calls to BookWyrm and OpenLibrary, which may add a small delay to page loading. The plugin uses WordPress's built-in caching mechanisms where possible.

