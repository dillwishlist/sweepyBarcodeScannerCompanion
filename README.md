# Sweepy
Sweepy Barcode Scanner Companion for Lansweeper

Also known as SBSC, or Sweepy

This project aims to make the tasks of inventory and asset info for a tech in the field possible with a mobile device.  Currently Lansweeper (https://www.lansweeper.com) does not have this functionality available.

Currently working:
- Look up assets based on their barcode field.
- Inventory all assets within a room.
- Update LastPhysicalInventortyTime (Custom15) to the tblAssetCustom table each time the asset is scanned
- Add a comment to the asset each time it's scanned

To Do:
- Add comment with old and new inventory information
- Allow asset lookup by serial number, MAC address, etc. in addition to barcode field
- Implement system to allow scanning of barcodes by the camera on a smartphone
- Update keystroke capture to be by JavaScript and not by a text input box.  Too easy to click out of
- Make a clear visual indicator when a barcode scans correctly
- Show more info about the asset directly in the page, and a link to load more in the full Lansweeper webpage (as a new tab)

How to inventory a room:
    - Create barcodes for each room in the format "ROOM_Location_Building_Department_Branch Office"
        * Spaces are okay
        * Replace the relevant information in this barcode with the field values you want in Lansweeper
        * Room prefix is defined in conf/config.php
    - Scan the room barcode
    - Scan barcode of each asset in room
        * Leading zeros will be stripped from the barcode automatically, therefore the BarCode field in Lansweeper should have these removed before hand.  This may be a future place for improvement.
    - Scan barcode for room again to exit inventory more
        * Any barcode starting with the "ROOM" prefix may be scanned to conclude the inventory
