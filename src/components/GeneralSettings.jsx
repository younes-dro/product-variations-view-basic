import React, { useState } from 'react';
import { Box, Switch, FormControlLabel, Checkbox, Grid2 } from '@mui/material';

const GeneralSettings = () => {
  const [isEnabled, setIsEnabled] = useState(true);
  const [showPrice, setShowPrice] = useState(true);
  const [showDescription, setShowDescription] = useState(true);

  const handleSave = async () => {
    const settings = {
      action: 'pvv_save_settings',
      security: pvv_ajax_params.nonce,
      is_enabled: isEnabled,
      show_price: showPrice,
      show_description: showDescription,
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
        alert('Settings saved successfully!');
      } else {
        alert('Failed to save settings: ' + result.data.message);
      }
    } catch (error) {
      alert('Error check the console');
      console.error('Error saving settings:', error);
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
            label="Enable Plugin"
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
          <Box mt={2}>
            <button onClick={handleSave}>Save Settings</button>
          </Box>
        </Grid2>
      </Grid2>
    </Box>
  );
};

export default GeneralSettings;
