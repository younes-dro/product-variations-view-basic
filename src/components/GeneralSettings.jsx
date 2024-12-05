import React, { useState } from 'react';
import { Box, Switch, FormControlLabel, Checkbox, Grid2, CircularProgress, Typography, Button } from '@mui/material';

const GeneralSettings = () => {
  const [isEnabled, setIsEnabled] = useState(pvv_ajax_params.settings.is_enabled);
  const [showPrice, setShowPrice] = useState(pvv_ajax_params.settings.show_price);
  const [showDescription, setShowDescription] = useState(pvv_ajax_params.settings.show_description);
  const [showProductGallery, setShowProductGallery] = useState(pvv_ajax_params.settings.show_product_gallery);
  const [loading, setLoading] = useState(false);
  const [statusMessage, setStatusMessage] = useState('');

  const handleSave = async () => {
    setLoading(true);
    setStatusMessage('');

    const settings = {
      action: 'pvv_save_settings',
      security: pvv_ajax_params.nonce,
      is_enabled: isEnabled,
      show_price: showPrice,
      show_description: showDescription,
      show_product_gallery: showProductGallery,
    };

    try {
      const response = await fetch(pvv_ajax_params.ajax_url, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams(settings).toString(),
      });

      const result = await response.json();

      if (result.success) {
        setStatusMessage('Settings saved successfully!');
      } else {
        setStatusMessage('Failed to save settings: ' + result.data.message);
      }
    } catch (error) {
      console.error('Error saving settings:', error);
      setStatusMessage('An error occurred. Please try again.');
    } finally {
      setLoading(false);
    }
  };

  return (
    <Box>
      <Grid2 container spacing={2} direction="column">
        <Grid2 item xs={12}>
          <FormControlLabel
            control={
              <Switch
                checked={isEnabled}
                onChange={() => setIsEnabled(!isEnabled)}
              />
            }
            label="Enable Frontend Display"
          />
        </Grid2>

        <Grid2 item xs={12}>
          <FormControlLabel
            control={
              <Checkbox
                checked={showPrice}
                onChange={() => setShowPrice(!showPrice)}
              />
            }
            label="Show Price"
          />
        </Grid2>

        <Grid2 item xs={12}>
          <FormControlLabel
            control={
              <Checkbox
                checked={showDescription}
                onChange={() => setShowDescription(!showDescription)}
              />
            }
            label="Show Short Description"
          />
        </Grid2>

        <Grid2 item xs={12}>
          <FormControlLabel
            control={
              <Checkbox
                checked={showProductGallery}
                onChange={() => setShowProductGallery(!showProductGallery)}
              />
            }
            label="Toggle Product Gallery Visibility"
          />
        </Grid2>

        <Grid2 item xs={12}>
          <Box mt={2} display="flex" alignItems="center">
            <Button
              onClick={handleSave}
              disabled={loading}
              variant="contained"
              color="primary"
              sx={{
                textTransform: 'none',
                fontWeight: 'bold',
                borderRadius: '8px',
                padding: '10px 20px',
              }}
            >
              {loading ? 'Saving...' : 'Save Settings'}
            </Button>
            {loading && <CircularProgress size={20} sx={{ marginLeft: '10px' }} />}
          </Box>
        </Grid2>

        <Grid2 item xs={12}>
          {statusMessage && (
            <Typography
              variant="body2"
              color={statusMessage.includes('success') ? 'green' : 'red'}
            >
              {statusMessage}
            </Typography>
          )}
        </Grid2>
      </Grid2>
    </Box>
  );
};

export default GeneralSettings;
