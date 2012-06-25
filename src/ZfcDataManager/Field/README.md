# ZfcDataManager FieldManager

In the ZfcDataManager, every model has its own FieldManager instance.
This allows the model to lazy-load every field (especially useful in cases where
the field represents another model or store)

